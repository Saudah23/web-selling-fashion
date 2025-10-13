<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SimulatePaymentSuccess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:simulate-success {order_number : Order number to simulate payment success}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simulate payment success for testing purposes (sandbox only)';

    protected MidtransService $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        parent::__construct();
        $this->midtransService = $midtransService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orderNumber = $this->argument('order_number');

        $this->info("Simulating payment success for order: {$orderNumber}");

        try {
            $order = Order::where('order_number', $orderNumber)
                ->with('paymentTransaction')
                ->first();

            if (!$order) {
                $this->error("Order {$orderNumber} not found.");
                return 1;
            }

            if (!$order->paymentTransaction) {
                $this->error("Order {$orderNumber} has no payment transaction.");
                return 1;
            }

            if ($order->status !== 'pending') {
                $this->warn("Order {$orderNumber} is not pending (current status: {$order->status}).");
                return 1;
            }

            $this->line("Current order status: {$order->status}");
            $this->line("Current payment status: {$order->paymentTransaction->status}");

            // Simulate Midtrans notification for settlement
            $fakeNotification = [
                'order_id' => $orderNumber,
                'transaction_status' => 'settlement',
                'fraud_status' => 'accept',
                'transaction_id' => $order->paymentTransaction->transaction_id,
                'payment_type' => $order->paymentTransaction->payment_type ?? 'bank_transfer',
                'settlement_time' => now()->toISOString(),
                '_simulation' => true
            ];

            $result = $this->midtransService->handleNotification($fakeNotification);

            if ($result) {
                $this->info("✓ Payment simulation successful!");
                $this->info("✓ Order status updated to: {$order->fresh()->status}");
                $this->info("✓ Payment status updated to: {$order->paymentTransaction->fresh()->status}");
                $this->info("✓ Paid at: {$order->fresh()->paid_at}");

                Log::info('Payment simulation via command', [
                    'order_number' => $orderNumber,
                    'command' => 'payments:simulate-success'
                ]);
            } else {
                $this->error("✗ Payment simulation failed!");
                return 1;
            }

        } catch (\Exception $e) {
            $this->error("✗ Error: " . $e->getMessage());

            Log::error('Payment simulation command failed', [
                'order_number' => $orderNumber,
                'error' => $e->getMessage()
            ]);

            return 1;
        }

        return 0;
    }
}
