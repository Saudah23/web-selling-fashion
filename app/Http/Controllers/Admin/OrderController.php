<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderController extends Controller
{
    /**
     * Display admin orders page
     */
    public function index()
    {
        return view('admin.orders.index');
    }

    /**
     * Get orders data for JSGrid
     */
    public function data(Request $request)
    {
        try {
            $query = Order::with(['user', 'items.product'])
                ->select('orders.*')
                ->latest();

            // Apply filters
            if ($request->has('order_number') && !empty($request->order_number)) {
                $query->where('order_number', 'like', '%' . $request->order_number . '%');
            }

            if ($request->has('status') && !empty($request->status)) {
                $query->where('status', $request->status);
            }

            if ($request->has('tracking_number') && !empty($request->tracking_number)) {
                $query->where('tracking_number', 'like', '%' . $request->tracking_number . '%');
            }

            if ($request->has('user_email') && !empty($request->user_email)) {
                $query->whereHas('user', function($q) use ($request) {
                    $q->where('email', 'like', '%' . $request->user_email . '%');
                });
            }

            // Date filter
            if ($request->has('created_date') && !empty($request->created_date)) {
                $query->whereDate('created_at', $request->created_date);
            }

            // Date range filter (fallback)
            if ($request->has('created_from') && !empty($request->created_from)) {
                $query->whereDate('created_at', '>=', $request->created_from);
            }

            if ($request->has('created_to') && !empty($request->created_to)) {
                $query->whereDate('created_at', '<=', $request->created_to);
            }

            $orders = $query->get();

            $data = $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->user ? $order->user->name : 'N/A',
                    'customer_email' => $order->user ? $order->user->email : 'N/A',
                    'total_amount' => $order->formatted_total_amount,
                    'total_amount_raw' => (float) $order->total_amount,
                    'status' => $order->status,
                    'status_badge' => $order->status_badge,
                    'shipping_courier' => $order->shipping_courier ?: 'N/A',
                    'tracking_number' => $order->tracking_number ?: 'N/A',
                    'created_at' => $order->created_at->format('d M Y H:i'),
                    'created_date' => $order->created_at->format('Y-m-d'),
                    'paid_at' => $order->paid_at ? $order->paid_at->format('d M Y H:i') : null,
                    'shipped_at' => $order->shipped_at ? $order->shipped_at->format('d M Y H:i') : null,
                    'delivered_at' => $order->delivered_at ? $order->delivered_at->format('d M Y H:i') : null,
                    'items_count' => $order->items->count(),
                    'can_process' => in_array($order->status, ['paid']),
                    'can_ship' => in_array($order->status, ['processing']),
                    'can_deliver' => in_array($order->status, ['shipped']),
                ];
            });

            return response()->json($data);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load orders: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show order details
     */
    public function show($id)
    {
        try {
            $order = Order::with(['user', 'items.product', 'paymentTransaction'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                    'user' => $order->user,
                    'total_amount' => $order->total_amount,
                    'formatted_total_amount' => $order->formatted_total_amount,
                    'subtotal' => $order->subtotal,
                    'formatted_subtotal' => $order->formatted_subtotal,
                    'shipping_cost' => $order->shipping_cost,
                    'formatted_shipping_cost' => $order->formatted_shipping_cost,
                    'tax_amount' => $order->tax_amount,
                    'shipping_address' => $order->shipping_address,
                    'shipping_service' => $order->shipping_service,
                    'shipping_courier' => $order->shipping_courier,
                    'shipping_etd' => $order->shipping_etd,
                    'tracking_number' => $order->tracking_number,
                    'payment_method' => $order->payment_method,
                    'notes' => $order->notes,
                    'created_at' => $order->created_at,
                    'paid_at' => $order->paid_at,
                    'shipped_at' => $order->shipped_at,
                    'delivered_at' => $order->delivered_at,
                    'items' => $order->items->map(function($item) {
                        return [
                            'id' => $item->id,
                            'product_name' => $item->product_name,
                            'product_sku' => $item->product_sku,
                            'quantity' => $item->quantity,
                            'product_price' => $item->product_price,
                            'formatted_product_price' => $item->formatted_product_price,
                            'subtotal' => $item->subtotal,
                            'formatted_subtotal' => $item->formatted_subtotal,
                            'product_image' => $item->product_image,
                            'product_attributes' => $item->product_attributes,
                            'product' => $item->product ? [
                                'id' => $item->product->id,
                                'name' => $item->product->name,
                                'current_price' => $item->product->price,
                                'stock_quantity' => $item->product->stock_quantity,
                                'is_active' => $item->product->is_active,
                            ] : null
                        ];
                    }),
                    'payment_transaction' => $order->paymentTransaction
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,processing,shipped,delivered,cancelled,refunded',
            'tracking_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000'
        ]);

        try {
            $order = Order::findOrFail($id);
            $oldStatus = $order->status;
            $newStatus = $request->status;

            DB::beginTransaction();

            // Update order data
            $updateData = [
                'status' => $newStatus,
            ];

            if ($request->filled('notes')) {
                $updateData['notes'] = $request->notes;
            }

            // Set timestamps based on status
            switch ($newStatus) {
                case 'paid':
                    if (!$order->paid_at) {
                        $updateData['paid_at'] = now();
                    }
                    break;
                case 'shipped':
                    if (!$order->shipped_at) {
                        $updateData['shipped_at'] = now();
                    }
                    if ($request->filled('tracking_number')) {
                        $updateData['tracking_number'] = $request->tracking_number;
                    }
                    break;
                case 'delivered':
                    if (!$order->delivered_at) {
                        $updateData['delivered_at'] = now();
                    }
                    if (!$order->shipped_at) {
                        $updateData['shipped_at'] = now();
                    }
                    break;
            }

            $order->update($updateData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Order status updated from {$oldStatus} to {$newStatus}",
                'data' => $order->fresh()
            ]);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update order status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update order status
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
            'status' => 'required|in:pending,paid,processing,shipped,delivered,cancelled,refunded',
            'tracking_numbers' => 'nullable|array',
            'notes' => 'nullable|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            $orderIds = $request->order_ids;
            $newStatus = $request->status;
            $trackingNumbers = $request->tracking_numbers ?: [];

            $updated = 0;
            foreach ($orderIds as $index => $orderId) {
                $order = Order::find($orderId);
                if (!$order) continue;

                $updateData = ['status' => $newStatus];

                if ($request->filled('notes')) {
                    $updateData['notes'] = $request->notes;
                }

                // Set timestamps based on status
                switch ($newStatus) {
                    case 'paid':
                        if (!$order->paid_at) {
                            $updateData['paid_at'] = now();
                        }
                        break;
                    case 'shipped':
                        if (!$order->shipped_at) {
                            $updateData['shipped_at'] = now();
                        }
                        if (isset($trackingNumbers[$index]) && !empty($trackingNumbers[$index])) {
                            $updateData['tracking_number'] = $trackingNumbers[$index];
                        }
                        break;
                    case 'delivered':
                        if (!$order->delivered_at) {
                            $updateData['delivered_at'] = now();
                        }
                        if (!$order->shipped_at) {
                            $updateData['shipped_at'] = now();
                        }
                        break;
                }

                $order->update($updateData);
                $updated++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully updated {$updated} orders to {$newStatus} status"
            ]);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to bulk update orders: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get order statistics for dashboard
     */
    public function statistics()
    {
        try {
            $stats = [
                'total_orders' => Order::count(),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'paid_orders' => Order::where('status', 'paid')->count(),
                'processing_orders' => Order::where('status', 'processing')->count(),
                'shipped_orders' => Order::where('status', 'shipped')->count(),
                'delivered_orders' => Order::where('status', 'delivered')->count(),
                'cancelled_orders' => Order::where('status', 'cancelled')->count(),
                'total_revenue' => Order::whereIn('status', ['paid', 'processing', 'shipped', 'delivered'])
                    ->sum('total_amount'),
                'today_orders' => Order::whereDate('created_at', today())->count(),
                'this_month_orders' => Order::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)->count(),
            ];

            // Format revenue
            $stats['formatted_total_revenue'] = 'Rp ' . number_format($stats['total_revenue'], 0, ',', '.');

            // Recent orders
            $stats['recent_orders'] = Order::with(['user', 'items'])
                ->latest()
                ->limit(5)
                ->get()
                ->map(function($order) {
                    return [
                        'id' => $order->id,
                        'order_number' => $order->order_number,
                        'customer_name' => $order->user->name ?? 'N/A',
                        'total_amount' => $order->formatted_total_amount,
                        'status' => $order->status,
                        'status_badge' => $order->status_badge,
                        'created_at' => $order->created_at->format('d M Y H:i'),
                        'items_count' => $order->items->count()
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load statistics: ' . $e->getMessage()
            ], 500);
        }
    }
}