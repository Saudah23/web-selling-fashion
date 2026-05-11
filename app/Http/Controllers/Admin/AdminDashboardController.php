<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::where('is_active', true)->count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $completedOrders = Order::whereIn('status', ['delivered', 'completed'])->count();
        $activeCustomers = User::where('role', 'customer')->count();
        $recentOrders = Order::with(['user', 'items'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalProducts',
            'pendingOrders',
            'completedOrders',
            'activeCustomers',
            'recentOrders'
        ));
    }
}
