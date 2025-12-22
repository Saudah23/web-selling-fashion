<?php

namespace App\Services;

use App\Models\SystemSetting;
use App\Models\Order;
use App\Models\PaymentTransaction;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    private ?string $serverKey;
    private ?string $clientKey;
    private ?string $merchantId;
    private string $environment;
    private string $baseUrl;

    public function __construct()
    {
        $this->serverKey = SystemSetting::get('midtrans_server_key')
            ?? config('services.midtrans.server_key')
            ?? env('MIDTRANS_SERVER_KEY');

        $this->clientKey = SystemSetting::get('midtrans_client_key')
            ?? config('services.midtrans.client_key')
            ?? env('MIDTRANS_CLIENT_KEY');

        $this->merchantId = SystemSetting::get('midtrans_merchant_id')
            ?? config('services.midtrans.merchant_id')
            ?? env('MIDTRANS_MERCHANT_ID');

        $this->environment = SystemSetting::get('midtrans_environment')
            ?? config('services.midtrans.environment')
            ?? env('MIDTRANS_ENVIRONMENT', 'sandbox')
            ?? 'sandbox';

        $this->baseUrl = $this->environment === 'production'
            ? 'https://app.midtrans.com/snap/v1'
            : 'https://app.sandbox.midtrans.com/snap/v1';

        // Skip validation during artisan commands to allow package discovery
        if (app()->runningInConsole() && !app()->runningUnitTests()) {
            return;
        }

        if (!$this->serverKey || !$this->clientKey || !$this->merchantId) {
            throw new Exception('Midtrans credentials not properly configured');
        }
    }

    /**
     * Create payment transaction
     */
    public function createTransaction(Order $order): array
    {
        try {
            $transactionDetails = [
                'order_id' => $order->order_number,
                'gross_amount' => (int) $order->total_amount
            ];

            $itemDetails = [];
            foreach ($order->items as $item) {
                $itemDetails[] = [
                    'id' => $item->product_id,
                    'price' => (int) $item->product_price,
                    'quantity' => $item->quantity,
                    'name' => $item->product_name
                ];
            }

            // Add shipping cost as separate item
            if ($order->shipping_cost > 0) {
                $itemDetails[] = [
                    'id' => 'shipping',
                    'price' => (int) $order->shipping_cost,
                    'quantity' => 1,
                    'name' => 'Shipping Cost - ' . $order->shipping_service
                ];
            }

            $customerDetails = [
                'first_name' => $order->user->name,
                'email' => $order->user->email,
                'phone' => $order->shipping_address['phone'] ?? '',
                'billing_address' => [
                    'first_name' => $order->user->name,
                    'email' => $order->user->email,
                    'phone' => $order->shipping_address['phone'] ?? '',
                    'address' => $order->shipping_address['address'] ?? '',
                    'city' => $order->shipping_address['city_name'] ?? '',
                    'postal_code' => $order->shipping_address['postal_code'] ?? '',
                    'country_code' => 'IDN'
                ],
                'shipping_address' => [
                    'first_name' => $order->shipping_address['recipient_name'] ?? $order->user->name,
                    'phone' => $order->shipping_address['phone'] ?? '',
                    'address' => $order->shipping_address['address'] ?? '',
                    'city' => $order->shipping_address['city_name'] ?? '',
                    'postal_code' => $order->shipping_address['postal_code'] ?? '',
                    'country_code' => 'IDN'
                ]
            ];

            $payload = [
                'transaction_details' => $transactionDetails,
                'item_details' => $itemDetails,
                'customer_details' => $customerDetails,
                'enabled_payments' => $this->getEnabledPayments(),
                'callbacks' => [
                    'finish' => route('checkout.success'),
                    'unfinish' => route('checkout.pending'),
                    'error' => route('checkout.error')
                ]
            ];

            $response = Http::withBasicAuth($this->serverKey, '')
                ->withHeaders(['accept' => 'application/json'])
                ->post($this->baseUrl . '/transactions', $payload);

            if (!$response->successful()) {
                throw new Exception('Midtrans API request failed: ' . $response->body());
            }

            $data = $response->json();

            // Create payment transaction record
            $paymentTransaction = PaymentTransaction::create([
                'order_id' => $order->order_number,
                'user_id' => $order->user_id,
                'transaction_id' => $order->order_number,
                'payment_type' => 'snap',
                'gross_amount' => $order->total_amount,
                'currency' => 'IDR',
                'status' => 'pending',
                'transaction_time' => now(),
                'midtrans_response' => $data,
                'customer_details' => $customerDetails,
                'item_details' => $itemDetails,
                'shipping_address' => $order->shipping_address,
                'shipping_cost' => $order->shipping_cost,
                'shipping_service' => $order->shipping_service,
                'payment_url' => $data['redirect_url'] ?? null
            ]);

            return [
                'success' => true,
                'transaction_id' => $order->order_number,
                'payment_url' => $data['redirect_url'],
                'token' => $data['token'],
                'payment_transaction' => $paymentTransaction
            ];

        } catch (Exception $e) {
            Log::error('Midtrans transaction creation failed', [
                'order_id' => $order->order_number,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Handle Midtrans notification/webhook
     */
    public function handleNotification(array $notification): bool
    {
        try {
            $orderId = $notification['order_id'] ?? null;
            $transactionStatus = $notification['transaction_status'] ?? null;
            $fraudStatus = $notification['fraud_status'] ?? null;

            if (!$orderId || !$transactionStatus) {
                throw new Exception('Invalid notification data');
            }

            // Find payment transaction
            $paymentTransaction = PaymentTransaction::where('order_id', $orderId)->first();
            if (!$paymentTransaction) {
                throw new Exception('Payment transaction not found: ' . $orderId);
            }

            // Update payment transaction
            $paymentTransaction->update([
                'transaction_id' => $notification['transaction_id'] ?? $paymentTransaction->transaction_id,
                'payment_type' => $notification['payment_type'] ?? $paymentTransaction->payment_type,
                'status' => $transactionStatus,
                'fraud_status' => $fraudStatus,
                'transaction_time' => isset($notification['transaction_time'])
                    ? \Carbon\Carbon::parse($notification['transaction_time'])
                    : $paymentTransaction->transaction_time,
                'settlement_time' => isset($notification['settlement_time'])
                    ? \Carbon\Carbon::parse($notification['settlement_time'])
                    : null,
                'midtrans_response' => array_merge($paymentTransaction->midtrans_response ?? [], $notification)
            ]);

            // Update order status based on payment status
            $order = $paymentTransaction->order;
            if ($order) {
                $this->updateOrderStatus($order, $transactionStatus, $fraudStatus);
            }

            return true;

        } catch (Exception $e) {
            Log::error('Midtrans notification handling failed', [
                'notification' => $notification,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Update order status based on payment status
     */
    private function updateOrderStatus(Order $order, string $transactionStatus, ?string $fraudStatus): void
    {
        Log::info('Updating order status', [
            'order_number' => $order->order_number,
            'current_status' => $order->status,
            'transaction_status' => $transactionStatus,
            'fraud_status' => $fraudStatus
        ]);

        switch ($transactionStatus) {
            case 'capture':
                if ($fraudStatus === 'accept') {
                    $order->markAsPaid();
                    Log::info('Order marked as paid (capture)', ['order_number' => $order->order_number]);
                }
                break;

            case 'settlement':
                $order->markAsPaid();
                Log::info('Order marked as paid (settlement)', ['order_number' => $order->order_number]);
                break;

            case 'pending':
                // Keep order as pending
                Log::info('Order kept as pending', ['order_number' => $order->order_number]);
                break;

            case 'deny':
            case 'cancel':
            case 'expire':
                $order->update(['status' => 'cancelled']);
                Log::info('Order cancelled', [
                    'order_number' => $order->order_number,
                    'reason' => $transactionStatus
                ]);
                break;

            case 'failure':
                $order->update(['status' => 'cancelled']);
                Log::info('Order cancelled due to failure', ['order_number' => $order->order_number]);
                break;

            default:
                Log::warning('Unknown transaction status', [
                    'order_number' => $order->order_number,
                    'transaction_status' => $transactionStatus
                ]);
                break;
        }
    }

    /**
     * Get transaction status from Midtrans
     */
    public function getTransactionStatus(string $orderId): array
    {
        try {
            $coreApiUrl = $this->environment === 'production'
                ? 'https://api.midtrans.com/v2'
                : 'https://api.sandbox.midtrans.com/v2';

            $response = Http::withBasicAuth($this->serverKey, '')
                ->get($coreApiUrl . '/' . $orderId . '/status');

            if (!$response->successful()) {
                throw new Exception('Failed to get transaction status: ' . $response->body());
            }

            return $response->json();

        } catch (Exception $e) {
            Log::error('Failed to get Midtrans transaction status', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Get enabled payment methods from system settings
     */
    private function getEnabledPayments(): array
    {
        $methods = SystemSetting::get('payment_methods');

        if (is_string($methods)) {
            $methods = json_decode($methods, true);
        }

        return is_array($methods) && !empty($methods)
            ? $methods
            : ['qris', 'gopay', 'shopeepay', 'credit_card', 'bca_va', 'bni_va', 'bri_va', 'echannel'];
    }

    /**
     * Get client key for frontend
     */
    public function getClientKey(): string
    {
        return $this->clientKey;
    }

    /**
     * Get environment
     */
    public function getEnvironment(): string
    {
        return $this->environment;
    }

    /**
     * Check payment status and update order if needed
     */
    public function checkAndUpdatePaymentStatus(string $orderId): array
    {
        try {
            // Get transaction status from Midtrans
            $transactionData = $this->getTransactionStatus($orderId);

            Log::info('Retrieved transaction status from Midtrans', [
                'order_id' => $orderId,
                'transaction_data' => $transactionData
            ]);

            // Find payment transaction
            $paymentTransaction = PaymentTransaction::where('order_id', $orderId)->first();
            if (!$paymentTransaction) {
                throw new Exception('Payment transaction not found: ' . $orderId);
            }

            // Update payment transaction with latest status
            $paymentTransaction->update([
                'status' => $transactionData['transaction_status'] ?? $paymentTransaction->status,
                'fraud_status' => $transactionData['fraud_status'] ?? $paymentTransaction->fraud_status,
                'settlement_time' => isset($transactionData['settlement_time'])
                    ? \Carbon\Carbon::parse($transactionData['settlement_time'])
                    : $paymentTransaction->settlement_time,
                'midtrans_response' => array_merge($paymentTransaction->midtrans_response ?? [], $transactionData)
            ]);

            // Update order status
            $order = $paymentTransaction->order;
            if ($order) {
                $this->updateOrderStatus(
                    $order,
                    $transactionData['transaction_status'],
                    $transactionData['fraud_status'] ?? null
                );
            }

            return [
                'success' => true,
                'transaction_status' => $transactionData['transaction_status'],
                'order_status' => $order ? $order->fresh()->status : null,
                'message' => 'Payment status updated successfully'
            ];

        } catch (Exception $e) {
            Log::error('Failed to check and update payment status', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to check payment status: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Test API connection
     */
    public function testConnection(): array
    {
        try {
            $coreApiUrl = $this->environment === 'production'
                ? 'https://api.midtrans.com/v2'
                : 'https://api.sandbox.midtrans.com/v2';

            $response = Http::withBasicAuth($this->serverKey, '')
                ->get($coreApiUrl . '/test-transaction-id/status');

            $data = $response->json();

            // Midtrans returns 200 with error in JSON for non-existent transactions
            if (isset($data['status_code']) && $data['status_code'] === '404') {
                return [
                    'success' => true,
                    'message' => 'Midtrans API connection successful (test endpoint responded correctly)',
                    'environment' => $this->environment
                ];
            }

            return [
                'success' => false,
                'message' => 'Unexpected response from Midtrans API: ' . json_encode($data),
                'environment' => $this->environment
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Midtrans API connection error: ' . $e->getMessage(),
                'environment' => $this->environment
            ];
        }
    }

}