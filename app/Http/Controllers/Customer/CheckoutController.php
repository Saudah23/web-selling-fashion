<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\RajaOngkirService;
use App\Services\MidtransService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    private RajaOngkirService $rajaOngkirService;
    private MidtransService $midtransService;

    public function __construct(RajaOngkirService $rajaOngkirService, MidtransService $midtransService)
    {
        $this->rajaOngkirService = $rajaOngkirService;
        $this->midtransService = $midtransService;
    }

    /**
     * Show checkout page
     */
    public function index()
    {
        // Get cart items
        $cartItems = Cart::with(['product.images'])
            ->forUser(Auth::id())
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty');
        }

        // Validate stock availability
        foreach ($cartItems as $item) {
            if (!$item->product->is_active) {
                return redirect()->route('cart.index')
                    ->with('error', 'Some products in your cart are no longer available');
            }

            if ($item->product->stock_quantity < $item->quantity) {
                return redirect()->route('cart.index')
                    ->with('error', "Insufficient stock for {$item->product->name}. Only {$item->product->stock_quantity} items available.");
            }
        }

        // Get user addresses
        $addresses = CustomerAddress::where('user_id', Auth::id())
            ->with(['province', 'city', 'district', 'village'])
            ->get();

        if ($addresses->isEmpty()) {
            return redirect()->route('addresses.index')
                ->with('error', 'Please add a shipping address before checkout');
        }

        $subtotal = $cartItems->sum('subtotal');

        return view('home.checkout', compact('cartItems', 'addresses', 'subtotal'));
    }

    /**
     * Calculate shipping costs for selected address
     */
    public function calculateShipping(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:customer_addresses,id'
        ]);

        try {
            $address = CustomerAddress::where('id', $request->address_id)
                ->where('user_id', Auth::id())
                ->with('city')
                ->first();

            if (!$address) {
                return response()->json([
                    'success' => false,
                    'message' => 'Address not found'
                ], 404);
            }

            // Validate address has RajaOngkir city ID
            if (!$address->city || !$address->city->rajaongkir_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Shipping not available for this city. Please contact support.'
                ], 400);
            }

            // Get origin city ID from system settings
            $originCityId = $this->rajaOngkirService->getOriginCityId();
            if (!$originCityId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Shipping origin not configured. Please contact support.'
                ], 500);
            }

            // Get cart items and calculate total weight
            $cartItems = Cart::with('product')->forUser(Auth::id())->get();
            $totalWeight = $this->rajaOngkirService->calculateTotalWeight($cartItems);

            // Get supported couriers
            $couriers = $this->rajaOngkirService->getSupportedCouriers();

            // Calculate shipping costs
            $shippingOptions = $this->rajaOngkirService->getShippingCost(
                $originCityId,
                $address->city->rajaongkir_id,
                $totalWeight,
                $couriers
            );

            return response()->json([
                'success' => true,
                'shipping_options' => $shippingOptions,
                'total_weight' => $totalWeight,
                'destination_city' => $address->city->name
            ]);

        } catch (Exception $e) {
            Log::error('Shipping calculation failed', [
                'address_id' => $request->address_id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Provide user-friendly error messages
            $userMessage = 'Failed to calculate shipping cost. Please try again.';

            if (str_contains($e->getMessage(), 'API key not configured')) {
                $userMessage = 'Shipping service is temporarily unavailable. Please contact support.';
            } elseif (str_contains($e->getMessage(), 'origin not configured')) {
                $userMessage = 'Shipping origin not configured. Please contact support.';
            } elseif (str_contains($e->getMessage(), 'No shipping options available')) {
                $userMessage = 'No shipping options available for your location. Please try a different address.';
            } elseif (str_contains($e->getMessage(), 'not available for this city')) {
                $userMessage = 'Shipping is not available for this city. Please contact support or choose a different address.';
            }

            return response()->json([
                'success' => false,
                'message' => $userMessage
            ], 500);
        }
    }

    /**
     * Process checkout and create order
     */
    public function process(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:customer_addresses,id',
            'shipping_service' => 'required|string',
            'shipping_cost' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            // Get cart items
            $cartItems = Cart::with(['product.images'])
                ->forUser(Auth::id())
                ->get();

            if ($cartItems->isEmpty()) {
                throw new Exception('Cart is empty');
            }

            // Get shipping address
            $address = CustomerAddress::where('id', $request->address_id)
                ->where('user_id', Auth::id())
                ->with(['province', 'city', 'district', 'village'])
                ->first();

            if (!$address) {
                throw new Exception('Shipping address not found');
            }

            // Parse shipping service to extract courier and ETD
            // Format bisa: "tiki:ECO" atau "jne:REG - 2-3 hari"
            $shippingService = $request->shipping_service;
            $shippingCourier = null;
            $shippingEtd = null;

            if (strpos($shippingService, ':') !== false) {
                // Format: "courier:service"
                list($courier, $service) = explode(':', $shippingService, 2);
                $shippingCourier = strtolower(trim($courier));

                // Jika ada " - " berarti ada ETD
                if (strpos($service, ' - ') !== false) {
                    list(, $etd) = explode(' - ', $service, 2);
                    $shippingEtd = trim($etd);
                }
            } else {
                // Format lama: "courier - ETD"
                $parts = explode(' - ', $shippingService);
                $shippingCourier = count($parts) >= 1 ? strtolower(trim($parts[0])) : null;
                $shippingEtd = count($parts) >= 2 ? trim($parts[1]) : null;
            }

            // Validate stock and calculate totals
            $subtotal = 0;
            foreach ($cartItems as $item) {
                if (!$item->product->is_active) {
                    throw new Exception("Product {$item->product->name} is no longer available");
                }

                if ($item->product->stock_quantity < $item->quantity) {
                    throw new Exception("Insufficient stock for {$item->product->name}");
                }

                $subtotal += $item->subtotal;
            }

            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'subtotal' => $subtotal,
                'shipping_cost' => $request->shipping_cost,
                'tax_amount' => 0, // Can be calculated based on settings
                'total_amount' => $subtotal + $request->shipping_cost,
                'status' => 'pending',
                'shipping_address' => [
                    'recipient_name' => $address->recipient_name,
                    'phone' => $address->recipient_phone,
                    'address' => $address->address_detail,
                    'province_name' => $address->province->name,
                    'city_name' => $address->city->name,
                    'district_name' => $address->district->name,
                    'village_name' => $address->village->name,
                    'postal_code' => $address->postal_code,
                    'city_id' => $address->city_id,
                    'rajaongkir_city_id' => $address->city->rajaongkir_id,
                    'notes' => $address->notes
                ],
                'shipping_service' => $request->shipping_service,
                'shipping_courier' => $shippingCourier,
                'shipping_etd' => $shippingEtd,
                'payment_method' => 'midtrans',
                'notes' => $request->notes,
                'metadata' => [
                    'checkout_ip' => request()->ip(),
                    'checkout_user_agent' => request()->userAgent(),
                    'checkout_timestamp' => now()->toISOString(),
                    'total_items' => $cartItems->sum('quantity'),
                    'total_weight' => $this->rajaOngkirService->calculateTotalWeight($cartItems),
                    'payment_gateway' => 'midtrans',
                    'currency' => 'IDR',
                    'shipping_cost_breakdown' => [
                        'service' => $request->shipping_service,
                        'courier' => $shippingCourier,
                        'etd' => $shippingEtd,
                        'cost' => $request->shipping_cost
                    ],
                    'customer_address_id' => $address->id,
                    'cart_items_count' => $cartItems->count()
                ]
            ]);

            // Create order items and update stock
            foreach ($cartItems as $item) {
                // Create order item with product snapshot
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'product_sku' => $item->product->sku,
                    'product_price' => $item->product->price,
                    'product_compare_price' => $item->product->compare_price,
                    'product_image' => $item->product->images->where('is_primary', true)->first()?->file_path,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->subtotal,
                    'product_attributes' => $item->product->attributes
                ]);

                // Update product stock
                $item->product->decrement('stock_quantity', $item->quantity);
            }

            // Create Midtrans payment transaction
            $paymentResult = $this->midtransService->createTransaction($order);

            if (!$paymentResult['success']) {
                throw new Exception('Failed to create payment transaction');
            }

            // Clear cart
            Cart::where('user_id', Auth::id())->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'order_number' => $order->order_number,
                'payment_url' => $paymentResult['payment_url'],
                'redirect_url' => route('checkout.payment', ['order' => $order->order_number])
            ]);

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Checkout process failed', [
                'user_id' => Auth::id(),
                'request_data' => $request->all(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show payment page
     */
    public function payment(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->with(['items.product', 'paymentTransaction'])
            ->first();

        if (!$order) {
            return redirect()->route('home')
                ->with('error', 'Order not found');
        }

        if ($order->status !== 'pending') {
            return redirect()->route('customer.orders.show', $order->id)
                ->with('info', 'This order has already been processed');
        }

        $paymentTransaction = $order->paymentTransaction;

        return view('home.payment', compact('order', 'paymentTransaction'));
    }

    /**
     * Handle successful payment
     */
    public function success(Request $request)
    {
        $orderNumber = $request->query('order_id');

        if ($orderNumber) {
            $order = Order::where('order_number', $orderNumber)
                ->where('user_id', Auth::id())
                ->first();

            if ($order) {
                return view('home.checkout-success', compact('order'));
            }
        }

        return view('home.checkout-success', ['order' => null]);
    }

    /**
     * Handle pending payment
     */
    public function pending(Request $request)
    {
        $orderNumber = $request->query('order_id');

        if ($orderNumber) {
            $order = Order::where('order_number', $orderNumber)
                ->where('user_id', Auth::id())
                ->first();

            if ($order) {
                return view('home.checkout-pending', compact('order'));
            }
        }

        return view('home.checkout-pending', ['order' => null]);
    }

    /**
     * Handle payment error
     */
    public function error(Request $request)
    {
        $orderNumber = $request->query('order_id');

        if ($orderNumber) {
            $order = Order::where('order_number', $orderNumber)
                ->where('user_id', Auth::id())
                ->first();

            if ($order) {
                return view('home.checkout-error', compact('order'));
            }
        }

        return view('home.checkout-error', ['order' => null]);
    }

    /**
     * Handle Midtrans notification webhook
     */
    public function notification(Request $request)
    {
        try {
            $notification = $request->all();

            // Log the notification for debugging
            Log::info('Midtrans notification received', [
                'notification' => $notification,
                'is_simulation' => isset($notification['_frontend_simulation'])
            ]);

            // Remove simulation marker before processing
            unset($notification['_frontend_simulation']);

            $result = $this->midtransService->handleNotification($notification);

            if ($result) {
                Log::info('Midtrans notification processed successfully', [
                    'order_id' => $notification['order_id'] ?? 'unknown'
                ]);
                return response()->json(['status' => 'ok']);
            }

            return response()->json(['status' => 'error'], 400);

        } catch (Exception $e) {
            Log::error('Midtrans notification handling failed', [
                'notification' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Check payment status from Midtrans API
     */
    public function checkPaymentStatus(string $orderNumber)
    {
        try {
            $order = Order::where('order_number', $orderNumber)
                ->where('user_id', Auth::id())
                ->with('paymentTransaction')
                ->firstOrFail();

            if (!$order->paymentTransaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment transaction not found'
                ], 404);
            }

            // Check payment status from Midtrans API
            $result = $this->midtransService->checkAndUpdatePaymentStatus($orderNumber);

            return response()->json($result);

        } catch (Exception $e) {
            Log::error('Payment status check failed', [
                'order_number' => $orderNumber,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to check payment status: ' . $e->getMessage()
            ], 500);
        }
    }


}
