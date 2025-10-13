@extends('layouts.app')

@section('title', 'Owner Dashboard - Fashion Marketplace')

{{-- Owner dashboard now uses owner-sidebar component from layout --}}

@section('content')
<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    <div>
        <h3 class="fw-bold mb-3">Owner Dashboard</h3>
        <h6 class="op-7 mb-2">Strategic overview and business insights</h6>
    </div>
    <div class="ms-md-auto py-2 py-md-0">
        <a href="{{ route('owner.settings.index') }}" class="btn btn-primary btn-round">
            <i class="fas fa-cog me-2"></i>
            System Settings
        </a>
    </div>
</div>

<div class="row">
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-primary bubble-shadow-small">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Total Customers</p>
                            <h4 class="card-title">{{ number_format($metrics['total_users']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-info bubble-shadow-small">
                            <i class="fas fa-tshirt"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Active Products</p>
                            <h4 class="card-title">{{ number_format($metrics['total_products']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-success bubble-shadow-small">
                            <i class="fas fa-luggage-cart"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Total Revenue</p>
                            <h4 class="card-title">Rp {{ number_format($metrics['total_revenue']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-secondary bubble-shadow-small">
                            <i class="far fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Total Orders</p>
                            <h4 class="card-title">{{ number_format($metrics['total_orders']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">Business Overview</div>
                    <div class="card-tools">
                        <a href="{{ route('owner.export-report') }}" class="btn btn-label-success btn-round btn-sm me-2">
                            <span class="btn-label">
                                <i class="fa fa-download"></i>
                            </span>
                            Export Report
                        </a>
                        <a href="{{ route('owner.analytics') }}" class="btn btn-label-info btn-round btn-sm">
                            <span class="btn-label">
                                <i class="fa fa-chart-line"></i>
                            </span>
                            View Analytics
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Recent Orders Activity</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentData['recent_orders']->take(5) as $order)
                                        <tr>
                                            <td class="text-muted small">{{ substr($order->order_number, -8) }}</td>
                                            <td class="small">{{ $order->user->name ?? 'N/A' }}</td>
                                            <td class="small text-success">Rp {{ number_format($order->total_amount) }}</td>
                                            <td>
                                                <span class="badge badge-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'pending' ? 'warning' : 'info') }} badge-sm">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">No recent orders</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Top Selling Products</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Sold</th>
                                        <th>Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentData['top_products'] as $product)
                                        <tr>
                                            <td class="small">{{ Str::limit($product->product_name, 20) }}</td>
                                            <td class="small text-info">{{ $product->total_sold }}</td>
                                            <td class="small text-success">Rp {{ number_format($product->total_revenue) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">No sales data</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <h6 class="text-muted mb-3">Quick Insights</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="icon icon-warning me-3">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">{{ number_format($metrics['pending_orders']) }}</h5>
                                        <p class="text-muted mb-0 small">Pending Orders</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="icon icon-danger me-3">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">{{ number_format($metrics['low_stock_products']) }}</h5>
                                        <p class="text-muted mb-0 small">Low Stock Items</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="icon icon-success me-3">
                                        <i class="fas fa-user-plus"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">{{ number_format($metrics['new_customers_this_month']) }}</h5>
                                        <p class="text-muted mb-0 small">New Customers</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="icon icon-primary me-3">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">{{ count($recentData['order_status_distribution']) }}</h5>
                                        <p class="text-muted mb-0 small">Order Statuses</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-primary card-round">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">{{ $metrics['period_label'] }} Revenue</div>
                    <div class="card-tools">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-label-light dropdown-toggle" type="button"
                                    id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                Period
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item {{ $period === 'current_month' ? 'active' : '' }}"
                                   href="{{ route('owner.dashboard', ['period' => 'current_month']) }}">This Month</a>
                                <a class="dropdown-item {{ $period === 'last_month' ? 'active' : '' }}"
                                   href="{{ route('owner.dashboard', ['period' => 'last_month']) }}">Last Month</a>
                                <a class="dropdown-item {{ $period === 'this_year' ? 'active' : '' }}"
                                   href="{{ route('owner.dashboard', ['period' => 'this_year']) }}">This Year</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-category">{{ $metrics['period_date_range'] }}</div>
            </div>
            <div class="card-body pb-0">
                <div class="mb-4 mt-2">
                    <h1>Rp {{ number_format($metrics['current_month_revenue']) }}</h1>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="text-center">
                            <p class="text-muted small mb-3">Last 7 Days Daily Revenue</p>
                            @php
                                $last7Days = [];
                                for($i = 6; $i >= 0; $i--) {
                                    $date = \Carbon\Carbon::now()->subDays($i);
                                    $revenue = collect($revenueTrends['daily'])->where('date', $date->format('M d'))->first()['revenue'] ?? 0;
                                    $last7Days[] = ['date' => $date->format('M d'), 'revenue' => $revenue];
                                }
                            @endphp

                            <div class="row text-center">
                                @foreach($last7Days as $day)
                                    <div class="col">
                                        <div class="mb-2">
                                            <small class="text-muted d-block">{{ $day['date'] }}</small>
                                            <strong class="text-primary">{{ number_format($day['revenue']/1000, 0) }}K</strong>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection