<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class OrderController extends Controller
{
    protected MidtransService $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }
    /**
     * Display customer orders list
     */
    public function index(Request $request)
    {
        $statusFilter = $request->get('status');
        $search = $request->get('search');

        $query = Order::with(['items.product', 'paymentTransaction'])
            ->forUser(Auth::id())
            ->latest();

        // Filter by status
        if ($statusFilter && in_array($statusFilter, ['pending', 'paid', 'processing', 'shipped', 'delivered', 'cancelled'])) {
            $query->byStatus($statusFilter);
        }

        // Search by order number
        if ($search) {
            $query->where('order_number', 'like', '%' . $search . '%');
        }

        $orders = $query->paginate(10);

        // Get order counts by status
        $statusCounts = [
            'all' => Order::forUser(Auth::id())->count(),
            'pending' => Order::forUser(Auth::id())->byStatus('pending')->count(),
            'paid' => Order::forUser(Auth::id())->byStatus('paid')->count(),
            'processing' => Order::forUser(Auth::id())->byStatus('processing')->count(),
            'shipped' => Order::forUser(Auth::id())->byStatus('shipped')->count(),
            'delivered' => Order::forUser(Auth::id())->byStatus('delivered')->count(),
            'cancelled' => Order::forUser(Auth::id())->byStatus('cancelled')->count(),
        ];

        // Get recommended products (popular products)
        $recommendedProducts = \App\Models\Product::with(['images'])
            ->active()
            ->inRandomOrder()
            ->limit(6)
            ->get();

        return view('home.orders.index', compact('orders', 'statusCounts', 'statusFilter', 'search', 'recommendedProducts'));
    }

    /**
     * Display order details
     */
    public function show(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->with(['items.product', 'paymentTransaction'])
            ->firstOrFail();

        return view('home.orders.show', compact('order'));
    }

    /**
     * Cancel order (only if status is pending)
     */
    public function cancel(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($order->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Order cannot be cancelled. Current status: ' . ucfirst($order->status));
        }

        $order->update(['status' => 'cancelled']);

        // Update payment transaction if exists
        if ($order->paymentTransaction) {
            $order->paymentTransaction->update(['status' => 'cancel']);
        }

        return redirect()->back()
            ->with('success', 'Order has been cancelled successfully.');
    }

    /**
     * Track order shipment
     */
    public function track(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->with(['items.product', 'paymentTransaction'])
            ->firstOrFail();

        if (!$order->tracking_number) {
            return redirect()->back()
                ->with('error', 'Tracking number is not available yet.');
        }

        // Generate tracking timeline
        $timeline = $this->generateOrderTimeline($order);

        return view('home.orders.track', compact('order', 'timeline'));
    }

    /**
     * Reorder - add all items from this order to cart
     */
    public function reorder(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->with('items.product')
            ->firstOrFail();

        $addedItems = 0;
        $unavailableItems = [];

        foreach ($order->items as $item) {
            $product = $item->product;

            // Check if product is still available
            if (!$product || !$product->is_active) {
                $unavailableItems[] = $item->product_name;
                continue;
            }

            // Check stock
            if ($product->stock_quantity < $item->quantity) {
                $unavailableItems[] = $item->product_name . ' (insufficient stock)';
                continue;
            }

            // Add to cart
            $existingCartItem = \App\Models\Cart::where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->first();

            if ($existingCartItem) {
                $newQuantity = $existingCartItem->quantity + $item->quantity;
                if ($newQuantity <= $product->stock_quantity) {
                    $existingCartItem->update(['quantity' => $newQuantity]);
                    $addedItems++;
                }
            } else {
                \App\Models\Cart::create([
                    'user_id' => Auth::id(),
                    'product_id' => $product->id,
                    'quantity' => $item->quantity
                ]);
                $addedItems++;
            }
        }

        $message = $addedItems . ' items added to cart.';
        if (!empty($unavailableItems)) {
            $message .= ' Some items are no longer available: ' . implode(', ', $unavailableItems);
        }

        return redirect()->route('cart.index')
            ->with('success', $message);
    }

    /**
     * Refresh order payment status from Midtrans
     */
    public function refreshPaymentStatus(string $orderNumber)
    {
        try {
            $order = Order::where('order_number', $orderNumber)
                ->where('user_id', Auth::id())
                ->with('paymentTransaction')
                ->firstOrFail();

            if (!$order->paymentTransaction) {
                return redirect()->back()
                    ->with('error', 'Payment transaction not found for this order.');
            }

            $result = $this->midtransService->checkAndUpdatePaymentStatus($orderNumber);

            if ($result['success']) {
                return redirect()->back()
                    ->with('success', 'Payment status refreshed. Order status: ' . ucfirst($result['order_status'] ?? 'unknown'));
            } else {
                return redirect()->back()
                    ->with('error', 'Failed to refresh payment status: ' . $result['message']);
            }

        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error('Payment status refresh failed', [
                'order_number' => $orderNumber,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to refresh payment status. Please try again.');
        }
    }

    /**
     * Show invoice page
     */
    public function invoice(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->with(['items.product', 'paymentTransaction'])
            ->firstOrFail();

        if ($order->status === 'pending') {
            return redirect()->back()
                ->with('error', 'Invoice is only available for paid orders.');
        }

        return view('home.orders.invoice', compact('order'));
    }

    /**
     * Generate order timeline for tracking
     */
    private function generateOrderTimeline(Order $order): array
    {
        $timeline = [];

        // Order placed
        $timeline[] = [
            'title' => 'Order Placed',
            'description' => 'Your order has been placed successfully',
            'datetime' => $order->created_at,
            'status' => 'completed',
            'icon' => 'fa-shopping-cart'
        ];

        // Payment
        if ($order->paid_at) {
            $timeline[] = [
                'title' => 'Payment Confirmed',
                'description' => 'Payment has been confirmed and processed',
                'datetime' => $order->paid_at,
                'status' => 'completed',
                'icon' => 'fa-credit-card'
            ];
        } else {
            $timeline[] = [
                'title' => 'Waiting for Payment',
                'description' => 'Please complete your payment',
                'datetime' => null,
                'status' => $order->status === 'pending' ? 'current' : 'pending',
                'icon' => 'fa-credit-card'
            ];
        }

        // Processing
        if (in_array($order->status, ['processing', 'shipped', 'delivered'])) {
            $timeline[] = [
                'title' => 'Order Processing',
                'description' => 'Your order is being prepared',
                'datetime' => $order->paid_at, // Assume processing starts after payment
                'status' => 'completed',
                'icon' => 'fa-cogs'
            ];
        } else {
            $timeline[] = [
                'title' => 'Order Processing',
                'description' => 'Your order will be prepared after payment',
                'datetime' => null,
                'status' => 'pending',
                'icon' => 'fa-cogs'
            ];
        }

        // Shipped
        if ($order->shipped_at) {
            $timeline[] = [
                'title' => 'Order Shipped',
                'description' => 'Your order has been shipped. Tracking: ' . ($order->tracking_number ?? 'N/A'),
                'datetime' => $order->shipped_at,
                'status' => 'completed',
                'icon' => 'fa-truck'
            ];
        } else {
            $timeline[] = [
                'title' => 'Order Shipped',
                'description' => 'Your order will be shipped soon',
                'datetime' => null,
                'status' => $order->status === 'shipped' ? 'current' : 'pending',
                'icon' => 'fa-truck'
            ];
        }

        // Delivered
        if ($order->delivered_at) {
            $timeline[] = [
                'title' => 'Order Delivered',
                'description' => 'Your order has been delivered successfully',
                'datetime' => $order->delivered_at,
                'status' => 'completed',
                'icon' => 'fa-check-circle'
            ];
        } else {
            $timeline[] = [
                'title' => 'Order Delivered',
                'description' => 'Your order will be delivered soon',
                'datetime' => null,
                'status' => $order->status === 'delivered' ? 'current' : 'pending',
                'icon' => 'fa-check-circle'
            ];
        }

        return $timeline;
    }

    /**
     * Mark order as delivered (customer confirmation)
     */
    public function markAsDelivered(string $orderNumber)
    {
        try {
            $order = Order::where('order_number', $orderNumber)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            // Check if order is in shipped status
            if ($order->status !== 'shipped') {
                return response()->json([
                    'success' => false,
                    'message' => 'Order cannot be marked as delivered. Current status: ' . ucfirst($order->status)
                ], 400);
            }

            // Update order status to delivered
            $order->update([
                'status' => 'delivered',
                'delivered_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order has been marked as delivered successfully.'
            ]);

        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error('Mark as delivered failed', [
                'order_number' => $orderNumber,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to mark order as delivered. Please try again.'
            ], 500);
        }
    }
}