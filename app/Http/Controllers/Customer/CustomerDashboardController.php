<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Wishlist;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class CustomerDashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Get order statistics
        $totalOrders = Order::forUser($userId)->count();
        $pendingOrders = Order::forUser($userId)->byStatus('pending')->count();
        $completedOrders = Order::forUser($userId)->byStatus('delivered')->count();

        // Get wishlist count
        $wishlistCount = Wishlist::where('user_id', $userId)->count();

        // Get cart count
        $cartCount = Cart::where('user_id', $userId)->sum('quantity');

        // Get recent orders
        $recentOrders = Order::with(['items.product', 'paymentTransaction'])
            ->forUser($userId)
            ->latest()
            ->take(5)
            ->get();

        // Calculate total spent
        $totalSpent = Order::forUser($userId)
            ->whereIn('status', ['paid', 'processing', 'shipped', 'delivered'])
            ->sum('total_amount');

        return view('customer.dashboard', compact(
            'totalOrders',
            'pendingOrders',
            'completedOrders',
            'wishlistCount',
            'cartCount',
            'recentOrders',
            'totalSpent'
        ));
    }
}