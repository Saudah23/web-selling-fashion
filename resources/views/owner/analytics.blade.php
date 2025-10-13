@extends('layouts.app')

@section('title', 'Business Analytics - Owner Dashboard')

@section('content')
<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    <div>
        <h3 class="fw-bold mb-3">Business Analytics</h3>
        <h6 class="op-7 mb-2">Deep insights into your marketplace performance</h6>
    </div>
    <div class="ms-md-auto py-2 py-md-0">
        <a href="{{ route('owner.dashboard') }}" class="btn btn-secondary btn-round me-2">
            <i class="fas fa-arrow-left me-2"></i>
            Back to Dashboard
        </a>
        <a href="{{ route('owner.export-report') }}?format=excel" class="btn btn-success btn-round">
            <i class="fas fa-download me-2"></i>
            Export Data
        </a>
    </div>
</div>

<!-- Business Insights & Alerts -->
@if(count($insights) > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card card-round">
            <div class="card-header">
                <h4 class="card-title">
                    <i class="fas fa-lightbulb text-warning me-2"></i>
                    Business Insights & Recommendations
                </h4>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($insights as $insight)
                        <div class="col-md-4 mb-3">
                            <div class="alert alert-{{ $insight['type'] === 'success' ? 'success' : 'warning' }} border-0">
                                <h6 class="alert-heading">
                                    <i class="fas fa-{{ $insight['type'] === 'success' ? 'check-circle' : 'exclamation-triangle' }} me-2"></i>
                                    {{ $insight['title'] }}
                                </h6>
                                <p class="mb-2">{{ $insight['message'] }}</p>
                                <small class="text-muted">
                                    <strong>Action:</strong> {{ $insight['action'] }}
                                </small>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Revenue Analysis Deep Dive -->
<div class="row mb-4">
    <div class="col-lg-6">
        <div class="card card-round h-100">
            <div class="card-header">
                <h4 class="card-title">Revenue by Hour of Day</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Hour</th>
                                <th class="text-end">Revenue</th>
                                <th class="text-end">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalRevenue = $advancedAnalytics['revenue_analysis']['by_hour']->sum('revenue'); @endphp
                            @foreach($advancedAnalytics['revenue_analysis']['by_hour'] as $hour)
                                @php $percentage = $totalRevenue > 0 ? ($hour->revenue / $totalRevenue) * 100 : 0; @endphp
                                <tr>
                                    <td>{{ sprintf('%02d:00', $hour->hour) }}</td>
                                    <td class="text-end">Rp {{ number_format($hour->revenue) }}</td>
                                    <td class="text-end">{{ number_format($percentage, 1) }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card card-round h-100">
            <div class="card-header">
                <h4 class="card-title">Revenue by Day of Week</h4>
            </div>
            <div class="card-body">
                @php $totalWeekRevenue = $advancedAnalytics['revenue_analysis']['by_day_of_week']->sum('revenue'); @endphp
                @foreach($advancedAnalytics['revenue_analysis']['by_day_of_week'] as $day)
                    @php $percentage = $totalWeekRevenue > 0 ? ($day['revenue'] / $totalWeekRevenue) * 100 : 0; @endphp
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <div>
                            <h6 class="mb-1">{{ $day['day'] }}</h6>
                            <small class="text-muted">{{ number_format($percentage, 1) }}% of weekly revenue</small>
                        </div>
                        <div class="text-end">
                            <strong class="text-primary">Rp {{ number_format($day['revenue']) }}</strong>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Customer Behavior Analysis -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card card-round h-100">
            <div class="card-header">
                <h4 class="card-title">Customer Segmentation Analysis</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Segment</th>
                                <th class="text-end">Customers</th>
                                <th class="text-end">Total Revenue</th>
                                <th class="text-end">Avg Order Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($advancedAnalytics['customer_behavior']['segmentation'] as $segment => $data)
                                <tr>
                                    <td>
                                        <span class="badge badge-{{ $segment === 'VIP Customer' ? 'primary' : ($segment === 'Loyal Customer' ? 'success' : 'info') }}">
                                            {{ $segment }}
                                        </span>
                                    </td>
                                    <td class="text-end">{{ number_format($data['count']) }}</td>
                                    <td class="text-end">Rp {{ number_format($data['total_revenue']) }}</td>
                                    <td class="text-end">Rp {{ number_format($data['avg_order_value']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card card-round h-100">
            <div class="card-header">
                <h4 class="card-title">Conversion Funnel</h4>
            </div>
            <div class="card-body">
                <div class="funnel-step mb-3 p-3 bg-light rounded">
                    <div class="d-flex justify-content-between">
                        <span>Registrations</span>
                        <strong>{{ number_format($advancedAnalytics['conversion_funnel']['registrations']) }}</strong>
                    </div>
                </div>
                <div class="funnel-step mb-3 p-3 bg-light rounded">
                    <div class="d-flex justify-content-between">
                        <span>First Orders</span>
                        <strong>{{ number_format($advancedAnalytics['conversion_funnel']['first_orders']) }}</strong>
                    </div>
                    <small class="text-success">{{ number_format($advancedAnalytics['conversion_funnel']['registration_to_order'], 1) }}% conversion</small>
                </div>
                <div class="funnel-step p-3 bg-light rounded">
                    <div class="d-flex justify-content-between">
                        <span>Completed Orders</span>
                        <strong>{{ number_format($advancedAnalytics['conversion_funnel']['completed_orders']) }}</strong>
                    </div>
                    <small class="text-success">{{ number_format($advancedAnalytics['conversion_funnel']['order_completion_rate'], 1) }}% completion</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Product Performance Deep Dive -->
<div class="row mb-4">
    <div class="col-lg-6">
        <div class="card card-round h-100">
            <div class="card-header">
                <h4 class="card-title">Product Velocity (Sales/Day)</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="text-end">Velocity</th>
                                <th class="text-end">Total Sold</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($advancedAnalytics['product_performance']['velocity'] as $product)
                                <tr>
                                    <td>{{ Str::limit($product['name'], 25) }}</td>
                                    <td class="text-end">
                                        <span class="badge badge-{{ $product['velocity'] > 1 ? 'success' : 'warning' }}">
                                            {{ $product['velocity'] }}/day
                                        </span>
                                    </td>
                                    <td class="text-end">{{ $product['total_sold'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card card-round h-100">
            <div class="card-header">
                <h4 class="card-title">Inventory Turnover Rate</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="text-end">Turnover</th>
                                <th class="text-end">Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($advancedAnalytics['product_performance']['inventory_turnover'] as $product)
                                <tr>
                                    <td>{{ Str::limit($product['name'], 25) }}</td>
                                    <td class="text-end">
                                        <span class="badge badge-{{ $product['turnover_rate'] > 2 ? 'success' : ($product['turnover_rate'] > 1 ? 'warning' : 'danger') }}">
                                            {{ $product['turnover_rate'] }}x
                                        </span>
                                    </td>
                                    <td class="text-end">{{ $product['stock'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Forecasting & Predictions -->
@if(count($forecasting['forecasts']) > 0)
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card card-round h-100">
            <div class="card-header">
                <h4 class="card-title">
                    <i class="fas fa-crystal-ball text-info me-2"></i>
                    Revenue Forecasting (Next 3 Months)
                </h4>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($forecasting['forecasts'] as $forecast)
                        <div class="col-md-4 mb-3">
                            <div class="card border-0 bg-light">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">{{ $forecast['month'] }}</h6>
                                    <h4 class="text-primary">Rp {{ number_format($forecast['forecasted_revenue']) }}</h4>
                                    <small class="text-muted">{{ $forecast['confidence'] }}% confidence</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-3 p-3 bg-light rounded">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-chart-line text-{{ $forecasting['trend_direction'] === 'up' ? 'success' : ($forecasting['trend_direction'] === 'down' ? 'danger' : 'warning') }} me-2"></i>
                        <span>
                            <strong>Trend:</strong>
                            {{ ucfirst($forecasting['trend_direction']) }}
                            ({{ number_format($forecasting['avg_growth_rate'], 1) }}% avg growth)
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card card-round h-100">
            <div class="card-header">
                <h4 class="card-title">Average Transaction Value Trend</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th class="text-end">Avg Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($advancedAnalytics['revenue_analysis']['avg_transaction_trends'] as $trend)
                                <tr>
                                    <td>{{ $trend['month'] }}</td>
                                    <td class="text-end">Rp {{ number_format($trend['avg_value']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Revenue & Payment Analytics -->
<div class="row mb-4">
    <div class="col-lg-7">
        <div class="card card-round h-100">
            <div class="card-header">
                <h4 class="card-title">Revenue Trends (Last 12 Months)</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th class="text-end">Revenue</th>
                                <th class="text-end">Growth</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($analyticsData['revenue']['monthly'] as $index => $month)
                                @php
                                    $growth = 0;
                                    if ($index > 0) {
                                        $previousMonth = $analyticsData['revenue']['monthly'][$index - 1];
                                        $previousRevenue = $previousMonth['revenue'];

                                        if ($previousRevenue > 0) {
                                            $growth = (($month['revenue'] - $previousRevenue) / $previousRevenue) * 100;
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $month['month'] }}</td>
                                    <td class="text-end">Rp {{ number_format($month['revenue']) }}</td>
                                    <td class="text-end">
                                        @if($index === 0)
                                            <span class="text-muted">-</span>
                                        @elseif($growth > 0)
                                            <span class="text-success">+{{ number_format($growth, 1) }}%</span>
                                        @elseif($growth < 0)
                                            <span class="text-danger">{{ number_format($growth, 1) }}%</span>
                                        @else
                                            <span class="text-muted">0%</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card card-round h-100">
            <div class="card-header">
                <h4 class="card-title">Payment Methods</h4>
            </div>
            <div class="card-body">
                @forelse($analyticsData['revenue']['by_payment_method'] as $payment)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="mb-1">{{ ucfirst($payment->payment_type ?? 'Unknown') }}</h6>
                            <small class="text-muted">{{ $payment->transaction_count }} transactions</small>
                        </div>
                        <div class="text-end">
                            <strong>Rp {{ number_format($payment->total_revenue) }}</strong>
                        </div>
                    </div>
                @empty
                    <p class="text-muted text-center">No payment data available</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Customer Analytics -->
<div class="row mb-4">
    <div class="col-lg-5">
        <div class="card card-round h-100">
            <div class="card-header">
                <h4 class="card-title">Customer Growth</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th class="text-end">New Customers</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($analyticsData['customers']['growth'] as $month)
                                <tr>
                                    <td>{{ $month['month'] }}</td>
                                    <td class="text-end">{{ number_format($month['new_customers']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card card-round h-100">
            <div class="card-header">
                <h4 class="card-title">Top Customers</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th class="text-end">Orders</th>
                                <th class="text-end">Total Spent</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($analyticsData['customers']['top_customers'] as $customer)
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $customer->name }}</strong>
                                            <small class="d-block text-muted">{{ Str::limit($customer->email, 25) }}</small>
                                        </div>
                                    </td>
                                    <td class="text-end">{{ $customer->order_count }}</td>
                                    <td class="text-end">Rp {{ number_format($customer->total_spent) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No customer data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Product & Category Analytics -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card card-round h-100">
            <div class="card-header">
                <h4 class="card-title">Top Performing Products</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th class="text-end">Sold</th>
                                <th class="text-end">Revenue</th>
                                <th class="text-end">Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($analyticsData['products']['products'] as $product)
                                <tr>
                                    <td>{{ Str::limit($product->name, 25) }}</td>
                                    <td><code class="small">{{ $product->sku }}</code></td>
                                    <td class="text-end">{{ number_format($product->total_sold) }}</td>
                                    <td class="text-end">Rp {{ number_format($product->total_revenue) }}</td>
                                    <td class="text-end">
                                        <span class="badge badge-{{ $product->stock_quantity < 10 ? 'danger' : 'success' }} badge-sm">
                                            {{ $product->stock_quantity }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No product data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card card-round h-100">
            <div class="card-header">
                <h4 class="card-title">Category Performance</h4>
            </div>
            <div class="card-body">
                @forelse($analyticsData['products']['categories'] as $category)
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <div>
                            <h6 class="mb-1">{{ $category->category_name }}</h6>
                            <small class="text-muted">{{ $category->total_sold }} items sold</small>
                        </div>
                        <div class="text-end">
                            <strong class="text-primary">Rp {{ number_format($category->total_revenue) }}</strong>
                        </div>
                    </div>
                @empty
                    <p class="text-muted text-center">No category data available</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Order Analytics -->
<div class="row">
    <div class="col-lg-6">
        <div class="card card-round h-100">
            <div class="card-header">
                <h4 class="card-title">Order Trends</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th class="text-end">Orders</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($analyticsData['orders']['trends'] as $month)
                                <tr>
                                    <td>{{ $month['month'] }}</td>
                                    <td class="text-end">{{ number_format($month['orders']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card card-round h-100">
            <div class="card-header">
                <h4 class="card-title">Order Status & Metrics</h4>
            </div>
            <div class="card-body">
                @foreach($analyticsData['orders']['status_distribution'] as $status => $count)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <span class="badge badge-{{ $status === 'delivered' ? 'success' : ($status === 'pending' ? 'warning' : 'info') }}">
                                {{ ucfirst($status) }}
                            </span>
                        </div>
                        <div>
                            <strong>{{ number_format($count) }}</strong>
                        </div>
                    </div>
                @endforeach

                <hr class="my-3">
                <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                    <div>
                        <strong>Average Order Value</strong>
                    </div>
                    <div>
                        <strong class="text-success">Rp {{ number_format($analyticsData['orders']['average_order_value']) }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection