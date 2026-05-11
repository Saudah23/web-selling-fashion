<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OwnerDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get period from request (default: current month)
        $period = $request->get('period', 'current_month');

        // Get key business metrics
        $metrics = $this->getBusinessMetrics($period);

        // Get recent activity data
        $recentData = $this->getRecentData();

        // Get revenue trends
        $revenueTrends = $this->getRevenueTrends();

        return view('owner.dashboard', compact('metrics', 'recentData', 'revenueTrends', 'period'));
    }

    private function getBusinessMetrics($period = 'current_month')
    {
        // Calculate period revenue and dates
        $periodData = $this->calculatePeriodData($period);

        return [
            // Total users (customers only)
            'total_users' => User::where('role', 'customer')->count(),

            // Total products
            'total_products' => Product::where('is_active', true)->count(),

            // Total revenue (from successful payments)
            'total_revenue' => PaymentTransaction::whereIn('status', ['settlement', 'capture'])
                ->sum('gross_amount'),

            // Total orders
            'total_orders' => Order::count(),

            // Period revenue (based on selected period)
            'current_month_revenue' => $periodData['revenue'],
            'period_label' => $periodData['label'],
            'period_date_range' => $periodData['date_range'],

            // Pending orders count
            'pending_orders' => Order::where('status', 'pending')->count(),

            // Active products with low stock
            'low_stock_products' => Product::where('is_active', true)
                ->whereColumn('stock_quantity', '<=', 'min_stock_level')
                ->count(),

            // This month new customers
            'new_customers_this_month' => User::where('role', 'customer')
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count(),
        ];
    }

    private function calculatePeriodData($period)
    {
        $now = Carbon::now();

        switch ($period) {
            case 'last_month':
                $startDate = $now->copy()->subMonth()->startOfMonth();
                $endDate = $now->copy()->subMonth()->endOfMonth();
                $label = 'Bulan Lalu';
                $dateRange = $startDate->translatedFormat('F Y');
                break;

            case 'this_year':
                $startDate = $now->copy()->startOfYear();
                $endDate = $now->copy()->endOfYear();
                $label = 'Tahun Ini';
                $dateRange = $now->format('Y');
                break;

            case 'current_month':
            default:
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                $label = 'Bulan Ini';
                $dateRange = $now->translatedFormat('F Y');
                break;
        }

        $revenue = PaymentTransaction::whereIn('status', ['settlement', 'capture'])
            ->whereBetween('settlement_time', [$startDate, $endDate])
            ->sum('gross_amount');

        return [
            'revenue' => $revenue,
            'label' => $label,
            'date_range' => $dateRange,
            'start_date' => $startDate,
            'end_date' => $endDate
        ];
    }

    private function getRecentData()
    {
        return [
            // Recent orders (last 10)
            'recent_orders' => Order::with(['user', 'paymentTransaction'])
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get(),

            // Top selling products (by order items)
            'top_products' => DB::table('order_items')
                ->select('order_items.product_name', DB::raw('SUM(order_items.quantity) as total_sold'), DB::raw('SUM(order_items.subtotal) as total_revenue'))
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('orders.status', '!=', 'cancelled')
                ->groupBy('order_items.product_name')
                ->orderBy('total_sold', 'desc')
                ->take(5)
                ->get(),

            // Order status distribution
            'order_status_distribution' => Order::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status')
                ->toArray(),
        ];
    }

    private function getRevenueTrends()
    {
        // Last 12 months revenue data
        $monthlyRevenue = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $revenue = PaymentTransaction::whereIn('status', ['settlement', 'capture'])
                ->whereMonth('settlement_time', $date->month)
                ->whereYear('settlement_time', $date->year)
                ->sum('gross_amount');

            $monthlyRevenue[] = [
                'month' => $date->translatedFormat('M Y'),
                'revenue' => $revenue
            ];
        }

        // Last 30 days daily revenue
        $dailyRevenue = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $revenue = PaymentTransaction::whereIn('status', ['settlement', 'capture'])
                ->whereDate('settlement_time', $date->toDateString())
                ->sum('gross_amount');

            $dailyRevenue[] = [
                'date' => $date->translatedFormat('d M'),
                'revenue' => $revenue
            ];
        }

        return [
            'monthly' => $monthlyRevenue,
            'daily' => $dailyRevenue
        ];
    }

    public function analytics(Request $request)
    {
        // Get period and comparison filters
        $period = $request->get('period', '12_months');
        $comparison = $request->get('comparison', 'previous_period');

        // Get analytics data
        $analyticsData = $this->getAnalyticsData();

        // Get advanced analytics
        $advancedAnalytics = $this->getAdvancedAnalytics($period, $comparison);

        // Get performance insights
        $insights = $this->getBusinessInsights();

        // Get forecasting data
        $forecasting = $this->getForecastingData();

        return view('owner.analytics', compact('analyticsData', 'advancedAnalytics', 'insights', 'forecasting', 'period', 'comparison'));
    }

    public function financialReports(Request $request)
    {
        // Get period from request (default: current month)
        $period = $request->get('period', 'current_month');
        $year = $request->get('year', date('Y'));

        // Get financial data
        $financialData = $this->getFinancialData($period, $year);

        return view('owner.financial-reports', compact('financialData', 'period', 'year'));
    }

    public function businessReports(Request $request)
    {
        // Get period from request (default: current month)
        $period = $request->get('period', 'current_month');
        $year = $request->get('year', date('Y'));

        // Get comprehensive business data
        $businessData = $this->getBusinessReportsData($period, $year);

        return view('owner.business-reports', compact('businessData', 'period', 'year'));
    }

    public function exportReport(Request $request)
    {
        $format = $request->get('format', 'pdf');
        $period = $request->get('period', 'current_month');

        // Get report data
        $reportData = $this->getReportData($period);

        if ($format === 'excel') {
            return $this->exportToExcel($reportData, $period);
        }

        return $this->exportToPdf($reportData, $period);
    }

    private function getAnalyticsData()
    {
        // Revenue analytics
        $revenueAnalytics = $this->getRevenueAnalytics();

        // Customer analytics
        $customerAnalytics = $this->getCustomerAnalytics();

        // Product analytics
        $productAnalytics = $this->getProductAnalytics();

        // Order analytics
        $orderAnalytics = $this->getOrderAnalytics();

        return [
            'revenue' => $revenueAnalytics,
            'customers' => $customerAnalytics,
            'products' => $productAnalytics,
            'orders' => $orderAnalytics
        ];
    }

    private function getRevenueAnalytics()
    {
        // Last 12 months revenue comparison
        $monthlyRevenue = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $revenue = PaymentTransaction::whereIn('status', ['settlement', 'capture'])
                ->whereMonth('settlement_time', $date->month)
                ->whereYear('settlement_time', $date->year)
                ->sum('gross_amount');

            $monthlyRevenue[] = [
                'month' => $date->translatedFormat('M Y'),
                'revenue' => $revenue,
                'short_month' => $date->format('M')
            ];
        }

        // Revenue by payment method
        $revenueByPayment = PaymentTransaction::whereIn('status', ['settlement', 'capture'])
            ->select('payment_type', DB::raw('SUM(gross_amount) as total_revenue'), DB::raw('COUNT(*) as transaction_count'))
            ->groupBy('payment_type')
            ->orderBy('total_revenue', 'desc')
            ->get();

        return [
            'monthly' => $monthlyRevenue,
            'by_payment_method' => $revenueByPayment
        ];
    }

    private function getCustomerAnalytics()
    {
        // Customer growth over last 12 months
        $customerGrowth = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $newCustomers = User::where('role', 'customer')
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();

            $customerGrowth[] = [
                'month' => $date->translatedFormat('M Y'),
                'new_customers' => $newCustomers,
                'short_month' => $date->format('M')
            ];
        }

        // Top customers by revenue
        $topCustomers = DB::table('payment_transactions')
            ->join('users', 'payment_transactions.user_id', '=', 'users.id')
            ->select('users.name', 'users.email', DB::raw('SUM(payment_transactions.gross_amount) as total_spent'), DB::raw('COUNT(payment_transactions.id) as order_count'))
            ->whereIn('payment_transactions.status', ['settlement', 'capture'])
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderBy('total_spent', 'desc')
            ->take(10)
            ->get();

        return [
            'growth' => $customerGrowth,
            'top_customers' => $topCustomers
        ];
    }

    private function getProductAnalytics()
    {
        // Product performance
        $productPerformance = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name', 'products.sku', DB::raw('SUM(order_items.quantity) as total_sold'), DB::raw('SUM(order_items.subtotal) as total_revenue'), 'products.stock_quantity')
            ->where('orders.status', '!=', 'cancelled')
            ->groupBy('products.id', 'products.name', 'products.sku', 'products.stock_quantity')
            ->orderBy('total_sold', 'desc')
            ->take(20)
            ->get();

        // Category performance
        $categoryPerformance = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name as category_name', DB::raw('SUM(order_items.quantity) as total_sold'), DB::raw('SUM(order_items.subtotal) as total_revenue'))
            ->where('orders.status', '!=', 'cancelled')
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_revenue', 'desc')
            ->get();

        return [
            'products' => $productPerformance,
            'categories' => $categoryPerformance
        ];
    }

    private function getOrderAnalytics()
    {
        // Order trends
        $orderTrends = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $orderCount = Order::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();

            $orderTrends[] = [
                'month' => $date->translatedFormat('M Y'),
                'orders' => $orderCount,
                'short_month' => $date->format('M')
            ];
        }

        // Order status distribution
        $statusDistribution = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        // Average order value
        $avgOrderValue = Order::whereNotIn('status', ['cancelled'])
            ->avg('total_amount');

        return [
            'trends' => $orderTrends,
            'status_distribution' => $statusDistribution,
            'average_order_value' => $avgOrderValue
        ];
    }

    private function getReportData($period)
    {
        $periodData = $this->calculatePeriodData($period);
        $startDate = $periodData['start_date'];
        $endDate = $periodData['end_date'];

        return [
            'period_info' => $periodData,
            'summary' => [
                'total_revenue' => PaymentTransaction::whereIn('status', ['settlement', 'capture'])
                    ->whereBetween('settlement_time', [$startDate, $endDate])
                    ->sum('gross_amount'),
                'total_orders' => Order::whereBetween('created_at', [$startDate, $endDate])->count(),
                'new_customers' => User::where('role', 'customer')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count(),
                'completed_orders' => Order::whereIn('status', ['delivered'])
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count(),
            ],
            'orders' => Order::with(['user', 'paymentTransaction'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'desc')
                ->get(),
            'customers' => User::where('role', 'customer')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'desc')
                ->get(),
            'top_products' => DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->select('order_items.product_name', DB::raw('SUM(order_items.quantity) as total_sold'), DB::raw('SUM(order_items.subtotal) as total_revenue'))
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->where('orders.status', '!=', 'cancelled')
                ->groupBy('order_items.product_name')
                ->orderBy('total_revenue', 'desc')
                ->take(10)
                ->get()
        ];
    }

    private function exportToPdf($reportData, $period)
    {
        // For now, return a simple text response
        // In production, you would use a package like dompdf or mpdf

        $filename = 'business_report_' . $period . '_' . date('Y-m-d') . '.txt';

        $content = "BUSINESS REPORT - " . strtoupper(str_replace('_', ' ', $period)) . "\n";
        $content .= "Generated: " . now()->format('Y-m-d H:i:s') . "\n";
        $content .= "Period: " . $reportData['period_info']['date_range'] . "\n\n";

        $content .= "SUMMARY\n";
        $content .= "=======\n";
        $content .= "Total Revenue: Rp " . number_format($reportData['summary']['total_revenue']) . "\n";
        $content .= "Total Orders: " . number_format($reportData['summary']['total_orders']) . "\n";
        $content .= "New Customers: " . number_format($reportData['summary']['new_customers']) . "\n";
        $content .= "Completed Orders: " . number_format($reportData['summary']['completed_orders']) . "\n\n";

        $content .= "TOP PRODUCTS\n";
        $content .= "============\n";
        foreach($reportData['top_products'] as $product) {
            $content .= $product->product_name . " - Sold: " . $product->total_sold . " - Revenue: Rp " . number_format($product->total_revenue) . "\n";
        }

        return response($content)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    private function getFinancialData($period, $year)
    {
        // Calculate date ranges
        $periodData = $this->calculatePeriodData($period);

        // Revenue breakdown
        $revenueBreakdown = $this->getRevenueBreakdown($periodData['start_date'], $periodData['end_date']);

        // Payment method analysis
        $paymentMethods = $this->getPaymentMethodAnalysis($periodData['start_date'], $periodData['end_date']);

        // Monthly comparison (current year)
        $monthlyComparison = $this->getMonthlyFinancialComparison($year);

        // Profit analysis
        $profitAnalysis = $this->getProfitAnalysis($periodData['start_date'], $periodData['end_date']);

        // Cash flow
        $cashFlow = $this->getCashFlowAnalysis($periodData['start_date'], $periodData['end_date']);

        // Outstanding payments
        $outstandingPayments = $this->getOutstandingPayments();

        return [
            'period_info' => [
                'period' => $period,
                'year' => $year,
                'date_range' => $periodData['label'] . ' (' . $periodData['start_date']->translatedFormat('d M') . ' - ' . $periodData['end_date']->translatedFormat('d M Y') . ')',
                'start_date' => $periodData['start_date'],
                'end_date' => $periodData['end_date']
            ],
            'revenue_breakdown' => $revenueBreakdown,
            'payment_methods' => $paymentMethods,
            'monthly_comparison' => $monthlyComparison,
            'profit_analysis' => $profitAnalysis,
            'cash_flow' => $cashFlow,
            'outstanding_payments' => $outstandingPayments
        ];
    }

    private function getRevenueBreakdown($startDate, $endDate)
    {
        // Total revenue from successful payments
        $totalRevenue = PaymentTransaction::whereIn('status', ['settlement', 'capture'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('gross_amount');

        // Revenue by status
        $revenueByStatus = PaymentTransaction::select('status', DB::raw('SUM(gross_amount) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('status')
            ->get();

        // Revenue by payment type
        $revenueByType = PaymentTransaction::select('payment_type', DB::raw('SUM(gross_amount) as total'))
            ->whereIn('status', ['settlement', 'capture'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('payment_type')
            ->get();

        return [
            'total_revenue' => $totalRevenue,
            'by_status' => $revenueByStatus,
            'by_type' => $revenueByType
        ];
    }

    private function getPaymentMethodAnalysis($startDate, $endDate)
    {
        return PaymentTransaction::select(
                'payment_type',
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(gross_amount) as total_amount'),
                DB::raw('AVG(gross_amount) as avg_amount')
            )
            ->whereIn('status', ['settlement', 'capture'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('payment_type')
            ->orderBy('total_amount', 'desc')
            ->get();
    }

    private function getMonthlyFinancialComparison($year)
    {
        $months = [];
        for ($month = 1; $month <= 12; $month++) {
            $startDate = Carbon::create($year, $month, 1)->startOfMonth();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth();

            $revenue = PaymentTransaction::whereIn('status', ['settlement', 'capture'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('gross_amount');

            $orders = Order::whereBetween('created_at', [$startDate, $endDate])->count();

            $months[] = [
                'month' => $startDate->format('M'),
                'month_number' => $month,
                'revenue' => $revenue,
                'orders' => $orders,
                'avg_order_value' => $orders > 0 ? $revenue / $orders : 0
            ];
        }

        return $months;
    }

    private function getProfitAnalysis($startDate, $endDate)
    {
        // For now, we'll use a simplified profit calculation
        // In real business, you'd have cost of goods sold, operational costs, etc.

        $totalRevenue = PaymentTransaction::whereIn('status', ['settlement', 'capture'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('gross_amount');

        // Assuming operational costs are 30% of revenue (placeholder)
        $estimatedCosts = $totalRevenue * 0.30;
        $estimatedProfit = $totalRevenue - $estimatedCosts;
        $profitMargin = $totalRevenue > 0 ? ($estimatedProfit / $totalRevenue) * 100 : 0;

        return [
            'total_revenue' => $totalRevenue,
            'estimated_costs' => $estimatedCosts,
            'estimated_profit' => $estimatedProfit,
            'profit_margin' => $profitMargin
        ];
    }

    private function getCashFlowAnalysis($startDate, $endDate)
    {
        // Daily cash flow for the period
        $dailyFlow = PaymentTransaction::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(CASE WHEN status IN ("settlement", "capture") THEN gross_amount ELSE 0 END) as inflow'),
                DB::raw('COUNT(CASE WHEN status IN ("settlement", "capture") THEN 1 END) as successful_transactions'),
                DB::raw('COUNT(CASE WHEN status = "pending" THEN 1 END) as pending_transactions')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        return [
            'daily_flow' => $dailyFlow,
            'total_inflow' => $dailyFlow->sum('inflow'),
            'avg_daily_inflow' => $dailyFlow->count() > 0 ? $dailyFlow->avg('inflow') : 0
        ];
    }

    private function getOutstandingPayments()
    {
        // Pending and failed payments
        $pendingPayments = PaymentTransaction::where('status', 'pending')
            ->with('order')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $failedPayments = PaymentTransaction::where('status', 'deny')
            ->with('order')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return [
            'pending' => $pendingPayments,
            'failed' => $failedPayments,
            'pending_amount' => PaymentTransaction::where('status', 'pending')->sum('gross_amount'),
            'failed_amount' => PaymentTransaction::where('status', 'deny')->sum('gross_amount')
        ];
    }

    private function getBusinessReportsData($period, $year)
    {
        // Calculate date ranges
        $periodData = $this->calculatePeriodData($period);

        // Core business metrics
        $businessMetrics = $this->getBusinessMetricsReport($periodData['start_date'], $periodData['end_date']);

        // Customer analytics
        $customerAnalytics = $this->getCustomerAnalyticsReport($periodData['start_date'], $periodData['end_date']);

        // Product performance
        $productPerformance = $this->getProductPerformanceReport($periodData['start_date'], $periodData['end_date']);

        // Order analytics
        $orderAnalytics = $this->getOrderAnalyticsReport($periodData['start_date'], $periodData['end_date']);

        // Market insights
        $marketInsights = $this->getMarketInsightsReport($periodData['start_date'], $periodData['end_date']);

        // Growth metrics
        $growthMetrics = $this->getGrowthMetricsReport($period, $year);

        // Executive summary
        $executiveSummary = $this->getExecutiveSummary($businessMetrics, $customerAnalytics, $productPerformance);

        return [
            'period_info' => [
                'period' => $period,
                'year' => $year,
                'date_range' => $periodData['label'] . ' (' . $periodData['start_date']->translatedFormat('d M') . ' - ' . $periodData['end_date']->translatedFormat('d M Y') . ')',
                'start_date' => $periodData['start_date'],
                'end_date' => $periodData['end_date']
            ],
            'executive_summary' => $executiveSummary,
            'business_metrics' => $businessMetrics,
            'customer_analytics' => $customerAnalytics,
            'product_performance' => $productPerformance,
            'order_analytics' => $orderAnalytics,
            'market_insights' => $marketInsights,
            'growth_metrics' => $growthMetrics
        ];
    }

    private function getBusinessMetricsReport($startDate, $endDate)
    {
        // Revenue metrics
        $totalRevenue = PaymentTransaction::whereIn('status', ['settlement', 'capture'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('gross_amount');

        $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])->count();
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Customer metrics
        $totalCustomers = User::where('role', 'customer')->count();
        $newCustomers = User::where('role', 'customer')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Product metrics
        $totalProducts = Product::where('is_active', true)->count();
        $soldProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->distinct('order_items.product_id')
            ->count();

        return [
            'revenue' => [
                'total' => $totalRevenue,
                'orders' => $totalOrders,
                'avg_order_value' => $avgOrderValue
            ],
            'customers' => [
                'total' => $totalCustomers,
                'new' => $newCustomers,
                'acquisition_rate' => $totalCustomers > 0 ? ($newCustomers / $totalCustomers) * 100 : 0
            ],
            'products' => [
                'total' => $totalProducts,
                'sold' => $soldProducts,
                'sell_through_rate' => $totalProducts > 0 ? ($soldProducts / $totalProducts) * 100 : 0
            ]
        ];
    }

    private function getCustomerAnalyticsReport($startDate, $endDate)
    {
        // Customer segmentation
        $customerSegments = DB::table('users')
            ->select(
                DB::raw('COUNT(*) as total_customers'),
                DB::raw('SUM(CASE WHEN orders_count = 0 THEN 1 ELSE 0 END) as no_orders'),
                DB::raw('SUM(CASE WHEN orders_count = 1 THEN 1 ELSE 0 END) as single_order'),
                DB::raw('SUM(CASE WHEN orders_count BETWEEN 2 AND 5 THEN 1 ELSE 0 END) as regular'),
                DB::raw('SUM(CASE WHEN orders_count > 5 THEN 1 ELSE 0 END) as loyal')
            )
            ->leftJoin(
                DB::raw('(SELECT user_id, COUNT(*) as orders_count FROM orders GROUP BY user_id) as order_counts'),
                'users.id', '=', 'order_counts.user_id'
            )
            ->where('users.role', 'customer')
            ->first();

        // Top customers by revenue
        $topCustomers = User::select('users.name', 'users.email', DB::raw('SUM(payment_transactions.gross_amount) as total_spent'))
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->join('payment_transactions', 'orders.id', '=', 'payment_transactions.order_id')
            ->where('payment_transactions.status', 'settlement')
            ->whereBetween('payment_transactions.created_at', [$startDate, $endDate])
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderBy('total_spent', 'desc')
            ->limit(10)
            ->get();

        // Customer geographic distribution
        $geoDistribution = DB::table('customer_addresses')
            ->join('provinces', 'customer_addresses.province_id', '=', 'provinces.id')
            ->join('users', 'customer_addresses.user_id', '=', 'users.id')
            ->select('provinces.name as province_name', DB::raw('COUNT(DISTINCT customer_addresses.user_id) as customer_count'))
            ->where('users.role', 'customer')
            ->groupBy('provinces.id', 'provinces.name')
            ->orderBy('customer_count', 'desc')
            ->limit(10)
            ->get();

        return [
            'segments' => $customerSegments,
            'top_customers' => $topCustomers,
            'geographic_distribution' => $geoDistribution
        ];
    }

    private function getProductPerformanceReport($startDate, $endDate)
    {
        // Best selling products
        $bestSellers = DB::table('order_items')
            ->select(
                'products.name as product_name',
                'products.price',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', '!=', 'cancelled')
            ->groupBy('products.id', 'products.name', 'products.price')
            ->orderBy('total_revenue', 'desc')
            ->limit(10)
            ->get();

        // Category performance
        $categoryPerformance = DB::table('order_items')
            ->select(
                'categories.name as category_name',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.subtotal) as total_revenue'),
                DB::raw('COUNT(DISTINCT products.id) as products_sold')
            )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', '!=', 'cancelled')
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_revenue', 'desc')
            ->get();

        // Low stock alerts
        $lowStock = Product::where('is_active', true)
            ->where('stock_quantity', '<=', 10)
            ->orderBy('stock_quantity', 'asc')
            ->limit(15)
            ->get();

        return [
            'best_sellers' => $bestSellers,
            'category_performance' => $categoryPerformance,
            'low_stock_alerts' => $lowStock
        ];
    }

    private function getOrderAnalyticsReport($startDate, $endDate)
    {
        // Order status distribution
        $statusDistribution = Order::select('status', DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('status')
            ->get();

        // Order fulfillment metrics
        $fulfillmentMetrics = [
            'total_orders' => Order::whereBetween('created_at', [$startDate, $endDate])->count(),
            'completed_orders' => Order::where('status', 'delivered')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'pending_orders' => Order::where('status', 'pending')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'cancelled_orders' => Order::where('status', 'cancelled')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count()
        ];

        // Average order processing time
        $avgProcessingTime = Order::select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_hours'))
            ->where('status', 'delivered')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->first();

        return [
            'status_distribution' => $statusDistribution,
            'fulfillment_metrics' => $fulfillmentMetrics,
            'avg_processing_time' => $avgProcessingTime->avg_hours ?? 0
        ];
    }

    private function getMarketInsightsReport($startDate, $endDate)
    {
        // Peak order times
        $peakTimes = Order::select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as order_count')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy(DB::raw('HOUR(created_at)'))
            ->orderBy('order_count', 'desc')
            ->limit(5)
            ->get();

        // Peak order days
        $peakDays = Order::select(
                DB::raw('DAYNAME(created_at) as day_name'),
                DB::raw('COUNT(*) as order_count')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy(DB::raw('DAYNAME(created_at)'))
            ->orderBy('order_count', 'desc')
            ->get();

        // Seasonal trends
        $seasonalTrends = Order::select(
                DB::raw('WEEK(created_at) as week_number'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total_amount) as total_revenue')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy(DB::raw('WEEK(created_at)'))
            ->orderBy('week_number')
            ->get();

        return [
            'peak_times' => $peakTimes,
            'peak_days' => $peakDays,
            'seasonal_trends' => $seasonalTrends
        ];
    }

    private function getGrowthMetricsReport($period, $year)
    {
        // Calculate previous period for comparison
        $currentData = $this->calculatePeriodData($period);

        // Get previous period data
        switch ($period) {
            case 'last_month':
                $prevPeriod = 'current_month';
                $prevData = $this->calculatePeriodData($prevPeriod);
                $prevData['start_date'] = $prevData['start_date']->subMonth();
                $prevData['end_date'] = $prevData['end_date']->subMonth();
                break;
            case 'this_year':
                $prevData = [
                    'start_date' => Carbon::create($year - 1)->startOfYear(),
                    'end_date' => Carbon::create($year - 1)->endOfYear()
                ];
                break;
            default: // current_month
                $prevData = [
                    'start_date' => Carbon::now()->subMonth()->startOfMonth(),
                    'end_date' => Carbon::now()->subMonth()->endOfMonth()
                ];
                break;
        }

        // Current period metrics
        $currentRevenue = PaymentTransaction::whereIn('status', ['settlement', 'capture'])
            ->whereBetween('created_at', [$currentData['start_date'], $currentData['end_date']])
            ->sum('gross_amount');

        $currentOrders = Order::whereBetween('created_at', [$currentData['start_date'], $currentData['end_date']])->count();

        // Previous period metrics
        $prevRevenue = PaymentTransaction::whereIn('status', ['settlement', 'capture'])
            ->whereBetween('created_at', [$prevData['start_date'], $prevData['end_date']])
            ->sum('gross_amount');

        $prevOrders = Order::whereBetween('created_at', [$prevData['start_date'], $prevData['end_date']])->count();

        // Calculate growth rates
        $revenueGrowth = $prevRevenue > 0 ? (($currentRevenue - $prevRevenue) / $prevRevenue) * 100 : 0;
        $orderGrowth = $prevOrders > 0 ? (($currentOrders - $prevOrders) / $prevOrders) * 100 : 0;

        return [
            'current' => [
                'revenue' => $currentRevenue,
                'orders' => $currentOrders
            ],
            'previous' => [
                'revenue' => $prevRevenue,
                'orders' => $prevOrders
            ],
            'growth' => [
                'revenue' => $revenueGrowth,
                'orders' => $orderGrowth
            ]
        ];
    }

    private function getExecutiveSummary($businessMetrics, $customerAnalytics, $productPerformance)
    {
        // Key insights and recommendations
        $insights = [];

        // Revenue insight
        if ($businessMetrics['revenue']['avg_order_value'] > 500000) {
            $insights[] = [
                'type' => 'success',
                'title' => 'Nilai Pesanan Rata-rata Tinggi',
                'message' => 'Nilai pesanan rata-rata Rp ' . number_format($businessMetrics['revenue']['avg_order_value']) . ' menunjukkan basis pelanggan premium'
            ];
        }

        // Customer acquisition insight
        if ($businessMetrics['customers']['acquisition_rate'] > 10) {
            $insights[] = [
                'type' => 'success',
                'title' => 'Akuisisi Pelanggan Kuat',
                'message' => 'Tingkat akuisisi pelanggan baru ' . number_format($businessMetrics['customers']['acquisition_rate'], 1) . '% menunjukkan pertumbuhan yang sehat'
            ];
        }

        // Product performance insight
        if ($businessMetrics['products']['sell_through_rate'] < 50) {
            $insights[] = [
                'type' => 'warning',
                'title' => 'Optimasi Produk Diperlukan',
                'message' => 'Hanya ' . number_format($businessMetrics['products']['sell_through_rate'], 1) . '% produk yang terjual. Pertimbangkan optimasi inventaris'
            ];
        }

        // Customer retention insight
        $loyalCustomers = $customerAnalytics['segments']->loyal ?? 0;
        $totalCustomers = $customerAnalytics['segments']->total_customers ?? 1;
        if (($loyalCustomers / $totalCustomers) * 100 > 20) {
            $insights[] = [
                'type' => 'success',
                'title' => 'Loyalitas Pelanggan Kuat',
                'message' => number_format(($loyalCustomers / $totalCustomers) * 100, 1) . '% pelanggan adalah pembeli setia yang berulang'
            ];
        }

        return [
            'insights' => $insights,
            'kpi_summary' => [
                'revenue' => $businessMetrics['revenue']['total'],
                'orders' => $businessMetrics['revenue']['orders'],
                'customers' => $businessMetrics['customers']['total'],
                'aov' => $businessMetrics['revenue']['avg_order_value']
            ]
        ];
    }

    private function exportToExcel($reportData, $period)
    {
        // For now, return a CSV response
        // In production, you would use a package like Laravel Excel

        $filename = 'business_report_' . $period . '_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($reportData) {
            $file = fopen('php://output', 'w');

            // Summary section
            fputcsv($file, ['BUSINESS REPORT SUMMARY']);
            fputcsv($file, ['Metric', 'Value']);
            fputcsv($file, ['Total Revenue', 'Rp ' . number_format($reportData['summary']['total_revenue'])]);
            fputcsv($file, ['Total Orders', number_format($reportData['summary']['total_orders'])]);
            fputcsv($file, ['New Customers', number_format($reportData['summary']['new_customers'])]);
            fputcsv($file, ['Completed Orders', number_format($reportData['summary']['completed_orders'])]);
            fputcsv($file, []);

            // Top products section
            fputcsv($file, ['TOP PRODUCTS']);
            fputcsv($file, ['Product Name', 'Total Sold', 'Total Revenue']);
            foreach($reportData['top_products'] as $product) {
                fputcsv($file, [$product->product_name, $product->total_sold, 'Rp ' . number_format($product->total_revenue)]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getAdvancedAnalytics($period, $comparison)
    {
        // Revenue Analysis with Trends
        $revenueAnalysis = $this->getRevenueAnalysis($period);

        // Customer Behavior Analysis
        $customerBehavior = $this->getCustomerBehaviorAnalysis();

        // Product Performance Deep Dive
        $productPerformance = $this->getProductPerformanceAnalysis();

        // Seasonal Trends
        $seasonalTrends = $this->getSeasonalTrends();

        // Conversion Funnel
        $conversionFunnel = $this->getConversionFunnel();

        return [
            'revenue_analysis' => $revenueAnalysis,
            'customer_behavior' => $customerBehavior,
            'product_performance' => $productPerformance,
            'seasonal_trends' => $seasonalTrends,
            'conversion_funnel' => $conversionFunnel
        ];
    }

    private function getRevenueAnalysis($period)
    {
        // Revenue by hour of day
        $revenueByHour = PaymentTransaction::whereIn('status', ['settlement', 'capture'])
            ->select(DB::raw('HOUR(settlement_time) as hour'), DB::raw('SUM(gross_amount) as revenue'))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Revenue by day of week
        $revenueByDayOfWeek = PaymentTransaction::whereIn('status', ['settlement', 'capture'])
            ->select(DB::raw('DAYOFWEEK(settlement_time) as day_of_week'), DB::raw('SUM(gross_amount) as revenue'))
            ->groupBy('day_of_week')
            ->orderBy('day_of_week')
            ->get()
            ->map(function($item) {
                $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                return [
                    'day' => $days[$item->day_of_week - 1] ?? 'Tidak Diketahui',
                    'revenue' => $item->revenue
                ];
            });

        // Average transaction value trends
        $avgTransactionTrends = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $avgTransaction = PaymentTransaction::whereIn('status', ['settlement', 'capture'])
                ->whereMonth('settlement_time', $date->month)
                ->whereYear('settlement_time', $date->year)
                ->avg('gross_amount');

            $avgTransactionTrends[] = [
                'month' => $date->translatedFormat('M Y'),
                'avg_value' => $avgTransaction ?? 0
            ];
        }

        return [
            'by_hour' => $revenueByHour,
            'by_day_of_week' => $revenueByDayOfWeek,
            'avg_transaction_trends' => $avgTransactionTrends
        ];
    }

    private function getCustomerBehaviorAnalysis()
    {
        // Customer segmentation by purchase frequency
        $customerSegmentation = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->select('users.id', 'users.name', DB::raw('COUNT(orders.id) as order_count'), DB::raw('SUM(orders.total_amount) as total_spent'))
            ->where('orders.status', '!=', 'cancelled')
            ->groupBy('users.id', 'users.name')
            ->get()
            ->map(function($customer) {
                if ($customer->order_count >= 10) {
                    $segment = 'VIP Customer';
                } elseif ($customer->order_count >= 5) {
                    $segment = 'Loyal Customer';
                } elseif ($customer->order_count >= 2) {
                    $segment = 'Regular Customer';
                } else {
                    $segment = 'New Customer';
                }

                return [
                    'name' => $customer->name,
                    'order_count' => $customer->order_count,
                    'total_spent' => $customer->total_spent,
                    'segment' => $segment
                ];
            })
            ->groupBy('segment')
            ->map(function($group) {
                return [
                    'count' => $group->count(),
                    'total_revenue' => $group->sum('total_spent'),
                    'avg_order_value' => $group->avg('total_spent') / max($group->avg('order_count'), 1)
                ];
            });

        // Purchase patterns by time
        $purchasePatterns = Order::select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('AVG(total_amount) as avg_amount')
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        return [
            'segmentation' => $customerSegmentation,
            'purchase_patterns' => $purchasePatterns
        ];
    }

    private function getProductPerformanceAnalysis()
    {
        // Product velocity (sales per day since launch)
        $productVelocity = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select(
                'products.name',
                'products.created_at as launch_date',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('DATEDIFF(NOW(), products.created_at) as days_since_launch')
            )
            ->where('orders.status', '!=', 'cancelled')
            ->groupBy('products.id', 'products.name', 'products.created_at')
            ->get()
            ->map(function($product) {
                $velocity = $product->days_since_launch > 0 ? $product->total_sold / $product->days_since_launch : 0;
                return [
                    'name' => $product->name,
                    'total_sold' => $product->total_sold,
                    'days_since_launch' => $product->days_since_launch,
                    'velocity' => round($velocity, 2)
                ];
            })
            ->sortByDesc('velocity')
            ->take(10);

        // Inventory turnover rate
        $inventoryTurnover = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select(
                'products.name',
                'products.stock_quantity',
                DB::raw('SUM(order_items.quantity) as total_sold')
            )
            ->where('orders.status', '!=', 'cancelled')
            ->where('products.stock_quantity', '>', 0)
            ->groupBy('products.id', 'products.name', 'products.stock_quantity')
            ->get()
            ->map(function($product) {
                $turnover = $product->stock_quantity > 0 ? $product->total_sold / $product->stock_quantity : 0;
                return [
                    'name' => $product->name,
                    'stock' => $product->stock_quantity,
                    'sold' => $product->total_sold,
                    'turnover_rate' => round($turnover, 2)
                ];
            })
            ->sortByDesc('turnover_rate')
            ->take(10);

        return [
            'velocity' => $productVelocity,
            'inventory_turnover' => $inventoryTurnover
        ];
    }

    private function getSeasonalTrends()
    {
        // Sales by month across years
        $monthlyTrends = Order::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->where('status', '!=', 'cancelled')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function($item) {
                $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                return [
                    'month' => $monthNames[$item->month - 1],
                    'year' => $item->year,
                    'month_year' => $monthNames[$item->month - 1] . ' ' . $item->year,
                    'order_count' => $item->order_count,
                    'revenue' => $item->revenue
                ];
            });

        return [
            'monthly_trends' => $monthlyTrends
        ];
    }

    private function getConversionFunnel()
    {
        // Conversion funnel based on available real data
        $totalUsers = User::where('role', 'customer')->count();
        $usersWithOrders = Order::distinct('user_id')->count('user_id');
        $completedOrders = Order::where('status', 'delivered')->count();

        return [
            'registrations' => $totalUsers,
            'first_orders' => $usersWithOrders,
            'completed_orders' => $completedOrders,
            'registration_to_order' => $totalUsers > 0 ? ($usersWithOrders / $totalUsers) * 100 : 0,
            'order_completion_rate' => $usersWithOrders > 0 ? ($completedOrders / $usersWithOrders) * 100 : 0
        ];
    }

    private function getBusinessInsights()
    {
        // Key insights and recommendations
        $insights = [];

        // Revenue growth insight
        $currentMonthRevenue = PaymentTransaction::whereIn('status', ['settlement', 'capture'])
            ->whereMonth('settlement_time', Carbon::now()->month)
            ->sum('gross_amount');

        $lastMonthRevenue = PaymentTransaction::whereIn('status', ['settlement', 'capture'])
            ->whereMonth('settlement_time', Carbon::now()->subMonth()->month)
            ->sum('gross_amount');

        if ($lastMonthRevenue > 0) {
            $revenueGrowth = (($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100;
            if ($revenueGrowth > 20) {
                $insights[] = [
                    'type' => 'success',
                    'title' => 'Excellent Revenue Growth',
                    'message' => 'Revenue increased by ' . number_format($revenueGrowth, 1) . '% this month. Great job!',
                    'action' => 'Consider expanding marketing efforts to sustain growth.'
                ];
            } elseif ($revenueGrowth < -10) {
                $insights[] = [
                    'type' => 'warning',
                    'title' => 'Revenue Decline',
                    'message' => 'Revenue decreased by ' . number_format(abs($revenueGrowth), 1) . '% this month.',
                    'action' => 'Review marketing strategies and customer feedback.'
                ];
            }
        }

        // Low stock insight
        $lowStockCount = Product::where('is_active', true)
            ->whereColumn('stock_quantity', '<=', 'min_stock_level')
            ->count();

        if ($lowStockCount > 5) {
            $insights[] = [
                'type' => 'warning',
                'title' => 'Low Stock Alert',
                'message' => $lowStockCount . ' products are running low on stock.',
                'action' => 'Review inventory and reorder popular items.'
            ];
        }

        // Customer growth insight
        $newCustomersThisMonth = User::where('role', 'customer')
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();

        $newCustomersLastMonth = User::where('role', 'customer')
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->count();

        if ($newCustomersThisMonth > $newCustomersLastMonth * 1.5) {
            $insights[] = [
                'type' => 'success',
                'title' => 'Customer Acquisition Boost',
                'message' => 'New customer registrations increased significantly this month.',
                'action' => 'Focus on customer retention strategies.'
            ];
        }

        return $insights;
    }

    private function getForecastingData()
    {
        // Simple forecasting based on historical trends
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $revenue = PaymentTransaction::whereIn('status', ['settlement', 'capture'])
                ->whereMonth('settlement_time', $date->month)
                ->whereYear('settlement_time', $date->year)
                ->sum('gross_amount');

            $monthlyRevenue[] = $revenue;
        }

        // Calculate simple linear trend
        $n = count($monthlyRevenue);
        if ($n >= 3) {
            $avgGrowth = 0;
            for ($i = 1; $i < $n; $i++) {
                if ($monthlyRevenue[$i-1] > 0) {
                    $growth = (($monthlyRevenue[$i] - $monthlyRevenue[$i-1]) / $monthlyRevenue[$i-1]) * 100;
                    $avgGrowth += $growth;
                }
            }
            $avgGrowth = $avgGrowth / ($n - 1);

            // Forecast next 3 months
            $lastRevenue = end($monthlyRevenue);
            $forecasts = [];
            for ($i = 1; $i <= 3; $i++) {
                $forecasted = $lastRevenue * pow((1 + $avgGrowth/100), $i);
                $forecasts[] = [
                    'month' => Carbon::now()->addMonths($i)->translatedFormat('M Y'),
                    'forecasted_revenue' => $forecasted,
                    'confidence' => max(60, 90 - ($i * 10)) // Decreasing confidence
                ];
            }

            return [
                'forecasts' => $forecasts,
                'trend_direction' => $avgGrowth > 0 ? 'up' : 'down',
                'avg_growth_rate' => $avgGrowth
            ];
        }

        return [
            'forecasts' => [],
            'trend_direction' => 'stable',
            'avg_growth_rate' => 0
        ];
    }
}