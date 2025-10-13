@extends('layouts.app')

@section('title', 'Business Reports - Fashion Marketplace')

@section('content')
<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    <div>
        <h3 class="fw-bold mb-3">Business Reports</h3>
        <h6 class="op-7 mb-2">Comprehensive business intelligence and performance analysis</h6>
    </div>
    <div class="ms-md-auto py-2 py-md-0">
        <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" id="periodDropdown" data-bs-toggle="dropdown">
                Period: {{ ucfirst(str_replace('_', ' ', $period)) }}
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item {{ $period === 'current_month' ? 'active' : '' }}"
                       href="{{ route('owner.business-reports', ['period' => 'current_month']) }}">This Month</a></li>
                <li><a class="dropdown-item {{ $period === 'last_month' ? 'active' : '' }}"
                       href="{{ route('owner.business-reports', ['period' => 'last_month']) }}">Last Month</a></li>
                <li><a class="dropdown-item {{ $period === 'this_year' ? 'active' : '' }}"
                       href="{{ route('owner.business-reports', ['period' => 'this_year']) }}">This Year</a></li>
            </ul>
        </div>
    </div>
</div>

{{-- Executive Summary --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card card-round">
            <div class="card-header">
                <h4 class="card-title">Executive Summary</h4>
                <p class="card-category">{{ $businessData['period_info']['date_range'] }}</p>
            </div>
            <div class="card-body">
                {{-- KPI Summary --}}
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="text-center">
                            <h5 class="text-success">Rp {{ number_format($businessData['executive_summary']['kpi_summary']['revenue']) }}</h5>
                            <small class="text-muted">Total Revenue</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h5 class="text-info">{{ number_format($businessData['executive_summary']['kpi_summary']['orders']) }}</h5>
                            <small class="text-muted">Total Orders</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h5 class="text-primary">{{ number_format($businessData['executive_summary']['kpi_summary']['customers']) }}</h5>
                            <small class="text-muted">Total Customers</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h5 class="text-warning">Rp {{ number_format($businessData['executive_summary']['kpi_summary']['aov']) }}</h5>
                            <small class="text-muted">Avg Order Value</small>
                        </div>
                    </div>
                </div>

                {{-- Key Insights --}}
                @if(count($businessData['executive_summary']['insights']) > 0)
                    <h6 class="mb-3">Key Business Insights</h6>
                    <div class="row">
                        @foreach($businessData['executive_summary']['insights'] as $insight)
                            <div class="col-md-6 mb-3">
                                <div class="alert alert-{{ $insight['type'] === 'success' ? 'success' : 'warning' }} mb-0">
                                    <strong>{{ $insight['title'] }}</strong><br>
                                    <small>{{ $insight['message'] }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Business Metrics --}}
    <div class="col-md-4 mb-4">
        <div class="card card-round h-100">
            <div class="card-header">
                <h4 class="card-title">Business Metrics</h4>
                <p class="card-category">Core performance indicators</p>
            </div>
            <div class="card-body">
                {{-- Revenue Metrics --}}
                <div class="mb-4">
                    <h6 class="text-muted">Revenue Performance</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Revenue</span>
                        <strong class="text-success">Rp {{ number_format($businessData['business_metrics']['revenue']['total']) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Orders</span>
                        <strong>{{ number_format($businessData['business_metrics']['revenue']['orders']) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Avg Order Value</span>
                        <strong class="text-warning">Rp {{ number_format($businessData['business_metrics']['revenue']['avg_order_value']) }}</strong>
                    </div>
                </div>

                {{-- Customer Metrics --}}
                <div class="mb-4">
                    <h6 class="text-muted">Customer Performance</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Customers</span>
                        <strong>{{ number_format($businessData['business_metrics']['customers']['total']) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>New Customers</span>
                        <strong class="text-info">{{ number_format($businessData['business_metrics']['customers']['new']) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Acquisition Rate</span>
                        <strong class="text-{{ $businessData['business_metrics']['customers']['acquisition_rate'] > 10 ? 'success' : 'warning' }}">
                            {{ number_format($businessData['business_metrics']['customers']['acquisition_rate'], 1) }}%
                        </strong>
                    </div>
                </div>

                {{-- Product Metrics --}}
                <div>
                    <h6 class="text-muted">Product Performance</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Products</span>
                        <strong>{{ number_format($businessData['business_metrics']['products']['total']) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Products Sold</span>
                        <strong>{{ number_format($businessData['business_metrics']['products']['sold']) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Sell-Through Rate</span>
                        <strong class="text-{{ $businessData['business_metrics']['products']['sell_through_rate'] > 50 ? 'success' : 'warning' }}">
                            {{ number_format($businessData['business_metrics']['products']['sell_through_rate'], 1) }}%
                        </strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Customer Analytics --}}
    <div class="col-md-4 mb-4">
        <div class="card card-round h-100">
            <div class="card-header">
                <h4 class="card-title">Customer Analytics</h4>
                <p class="card-category">Customer behavior insights</p>
            </div>
            <div class="card-body">
                {{-- Customer Segmentation --}}
                <div class="mb-4">
                    <h6 class="text-muted">Customer Segmentation</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>No Orders</span>
                        <strong>{{ number_format($businessData['customer_analytics']['segments']->no_orders ?? 0) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Single Order</span>
                        <strong>{{ number_format($businessData['customer_analytics']['segments']->single_order ?? 0) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Regular (2-5 orders)</span>
                        <strong class="text-info">{{ number_format($businessData['customer_analytics']['segments']->regular ?? 0) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Loyal (5+ orders)</span>
                        <strong class="text-success">{{ number_format($businessData['customer_analytics']['segments']->loyal ?? 0) }}</strong>
                    </div>
                </div>

                {{-- Top Customers --}}
                <div class="mb-4">
                    <h6 class="text-muted">Top Customers (by Revenue)</h6>
                    <div style="max-height: 200px; overflow-y: auto;">
                        @forelse($businessData['customer_analytics']['top_customers']->take(5) as $customer)
                            <div class="d-flex justify-content-between mb-2">
                                <div>
                                    <span class="small fw-bold">{{ Str::limit($customer->name, 15) }}</span><br>
                                    <small class="text-muted">{{ Str::limit($customer->email, 20) }}</small>
                                </div>
                                <strong class="text-success">Rp {{ number_format($customer->total_spent) }}</strong>
                            </div>
                        @empty
                            <p class="text-muted small">No customer data available</p>
                        @endforelse
                    </div>
                </div>

                {{-- Geographic Distribution --}}
                <div>
                    <h6 class="text-muted">Geographic Distribution</h6>
                    <div style="max-height: 150px; overflow-y: auto;">
                        @forelse($businessData['customer_analytics']['geographic_distribution']->take(5) as $geo)
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small">{{ $geo->province_name }}</span>
                                <strong class="small">{{ $geo->customer_count }}</strong>
                            </div>
                        @empty
                            <p class="text-muted small">No geographic data available</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Growth Metrics --}}
    <div class="col-md-4 mb-4">
        <div class="card card-round h-100">
            <div class="card-header">
                <h4 class="card-title">Growth Analysis</h4>
                <p class="card-category">Period over period comparison</p>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h6 class="text-muted">Revenue Growth</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Current Period</span>
                        <strong class="text-success">Rp {{ number_format($businessData['growth_metrics']['current']['revenue']) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Previous Period</span>
                        <strong class="text-muted">Rp {{ number_format($businessData['growth_metrics']['previous']['revenue']) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Growth Rate</span>
                        <strong class="text-{{ $businessData['growth_metrics']['growth']['revenue'] >= 0 ? 'success' : 'danger' }}">
                            {{ $businessData['growth_metrics']['growth']['revenue'] >= 0 ? '+' : '' }}{{ number_format($businessData['growth_metrics']['growth']['revenue'], 1) }}%
                        </strong>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="text-muted">Order Growth</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Current Period</span>
                        <strong class="text-info">{{ number_format($businessData['growth_metrics']['current']['orders']) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Previous Period</span>
                        <strong class="text-muted">{{ number_format($businessData['growth_metrics']['previous']['orders']) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Growth Rate</span>
                        <strong class="text-{{ $businessData['growth_metrics']['growth']['orders'] >= 0 ? 'success' : 'danger' }}">
                            {{ $businessData['growth_metrics']['growth']['orders'] >= 0 ? '+' : '' }}{{ number_format($businessData['growth_metrics']['growth']['orders'], 1) }}%
                        </strong>
                    </div>
                </div>

                <div class="progress mb-2">
                    <div class="progress-bar bg-{{ $businessData['growth_metrics']['growth']['revenue'] >= 0 ? 'success' : 'danger' }}"
                         style="width: {{ min(abs($businessData['growth_metrics']['growth']['revenue']), 100) }}%"></div>
                </div>
                <small class="text-muted">Revenue growth indicator</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Product Performance --}}
    <div class="col-md-6 mb-4">
        <div class="card card-round h-100">
            <div class="card-header">
                <h4 class="card-title">Product Performance</h4>
                <p class="card-category">Best sellers and category analysis</p>
            </div>
            <div class="card-body">
                {{-- Best Sellers --}}
                <div class="mb-4">
                    <h6 class="text-muted">Best Selling Products</h6>
                    <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Sold</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($businessData['product_performance']['best_sellers'] as $product)
                                    <tr>
                                        <td class="small">{{ Str::limit($product->product_name, 20) }}</td>
                                        <td class="small">{{ $product->total_sold }}</td>
                                        <td class="small text-success">Rp {{ number_format($product->total_revenue) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No product sales data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Category Performance --}}
                <div>
                    <h6 class="text-muted">Category Performance</h6>
                    <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Products</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($businessData['product_performance']['category_performance'] as $category)
                                    <tr>
                                        <td class="small">{{ $category->category_name }}</td>
                                        <td class="small">{{ $category->products_sold }}</td>
                                        <td class="small text-success">Rp {{ number_format($category->total_revenue) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No category data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Order Analytics --}}
    <div class="col-md-6 mb-4">
        <div class="card card-round h-100">
            <div class="card-header">
                <h4 class="card-title">Order Analytics</h4>
                <p class="card-category">Order fulfillment insights</p>
            </div>
            <div class="card-body">
                {{-- Order Status Distribution --}}
                <div class="mb-4">
                    <h6 class="text-muted">Order Status Distribution</h6>
                    @foreach($businessData['order_analytics']['status_distribution'] as $status)
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-capitalize">{{ $status->status }}</span>
                            <strong class="badge badge-{{ $status->status === 'delivered' ? 'success' : ($status->status === 'pending' ? 'warning' : 'info') }}">
                                {{ $status->count }}
                            </strong>
                        </div>
                    @endforeach
                </div>

                {{-- Fulfillment Metrics --}}
                <div class="mb-4">
                    <h6 class="text-muted">Fulfillment Metrics</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Orders</span>
                        <strong>{{ number_format($businessData['order_analytics']['fulfillment_metrics']['total_orders']) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Completed</span>
                        <strong class="text-success">{{ number_format($businessData['order_analytics']['fulfillment_metrics']['completed_orders']) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Pending</span>
                        <strong class="text-warning">{{ number_format($businessData['order_analytics']['fulfillment_metrics']['pending_orders']) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Cancelled</span>
                        <strong class="text-danger">{{ number_format($businessData['order_analytics']['fulfillment_metrics']['cancelled_orders']) }}</strong>
                    </div>
                </div>

                {{-- Processing Time --}}
                <div>
                    <h6 class="text-muted">Average Processing Time</h6>
                    <h4 class="text-info">{{ number_format($businessData['order_analytics']['avg_processing_time'], 1) }} hours</h4>
                    <small class="text-muted">From order to delivery</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Market Insights --}}
    <div class="col-md-6 mb-4">
        <div class="card card-round h-100">
            <div class="card-header">
                <h4 class="card-title">Market Insights</h4>
                <p class="card-category">Customer behavior patterns</p>
            </div>
            <div class="card-body">
                {{-- Peak Order Times --}}
                <div class="mb-4">
                    <h6 class="text-muted">Peak Order Hours</h6>
                    @foreach($businessData['market_insights']['peak_times'] as $time)
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ $time->hour }}:00 - {{ $time->hour + 1 }}:00</span>
                            <strong class="text-info">{{ $time->order_count }} orders</strong>
                        </div>
                    @endforeach
                </div>

                {{-- Peak Order Days --}}
                <div>
                    <h6 class="text-muted">Peak Order Days</h6>
                    @foreach($businessData['market_insights']['peak_days'] as $day)
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ $day->day_name }}</span>
                            <strong class="text-success">{{ $day->order_count }} orders</strong>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Low Stock Alerts --}}
    <div class="col-md-6 mb-4">
        <div class="card card-round h-100">
            <div class="card-header">
                <h4 class="card-title">Inventory Alerts</h4>
                <p class="card-category">Low stock warnings</p>
            </div>
            <div class="card-body">
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Stock</th>
                                <th>Price</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($businessData['product_performance']['low_stock_alerts'] as $product)
                                <tr>
                                    <td class="small">{{ Str::limit($product->name, 20) }}</td>
                                    <td class="small">
                                        <span class="badge badge-{{ $product->stock_quantity <= 5 ? 'danger' : 'warning' }}">
                                            {{ $product->stock_quantity }}
                                        </span>
                                    </td>
                                    <td class="small">Rp {{ number_format($product->price) }}</td>
                                    <td class="small">
                                        <span class="text-{{ $product->stock_quantity <= 5 ? 'danger' : 'warning' }}">
                                            {{ $product->stock_quantity <= 5 ? 'Critical' : 'Low' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">All products have adequate stock</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection