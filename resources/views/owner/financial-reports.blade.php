@extends('layouts.app')

@section('title', 'Financial Reports - Fashion Marketplace')

@section('content')
<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    <div>
        <h3 class="fw-bold mb-3">Financial Reports</h3>
        <h6 class="op-7 mb-2">Comprehensive financial analysis and reporting</h6>
    </div>
    <div class="ms-md-auto py-2 py-md-0">
        <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" id="periodDropdown" data-bs-toggle="dropdown">
                Period: {{ ucfirst(str_replace('_', ' ', $period)) }}
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item {{ $period === 'current_month' ? 'active' : '' }}"
                       href="{{ route('owner.financial-reports', ['period' => 'current_month']) }}">This Month</a></li>
                <li><a class="dropdown-item {{ $period === 'last_month' ? 'active' : '' }}"
                       href="{{ route('owner.financial-reports', ['period' => 'last_month']) }}">Last Month</a></li>
                <li><a class="dropdown-item {{ $period === 'this_year' ? 'active' : '' }}"
                       href="{{ route('owner.financial-reports', ['period' => 'this_year']) }}">This Year</a></li>
            </ul>
        </div>
    </div>
</div>

{{-- Financial Summary Cards --}}
<div class="row mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-success bubble-shadow-small">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Total Revenue</p>
                            <h4 class="card-title">Rp {{ number_format($financialData['revenue_breakdown']['total_revenue']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-info bubble-shadow-small">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Est. Profit</p>
                            <h4 class="card-title">Rp {{ number_format($financialData['profit_analysis']['estimated_profit']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-warning bubble-shadow-small">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Pending Payments</p>
                            <h4 class="card-title">Rp {{ number_format($financialData['outstanding_payments']['pending_amount']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-secondary bubble-shadow-small">
                            <i class="fas fa-percentage"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Profit Margin</p>
                            <h4 class="card-title">{{ number_format($financialData['profit_analysis']['profit_margin'], 1) }}%</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Revenue Breakdown --}}
    <div class="col-md-6 mb-4">
        <div class="card card-round h-100">
            <div class="card-header">
                <h4 class="card-title">Revenue Breakdown</h4>
                <p class="card-category">{{ $financialData['period_info']['date_range'] }}</p>
            </div>
            <div class="card-body">
                {{-- By Payment Status --}}
                <h6 class="mb-3">By Payment Status</h6>
                @foreach($financialData['revenue_breakdown']['by_status'] as $status)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-capitalize">{{ $status->status }}</span>
                        <strong class="text-{{ $status->status === 'settlement' || $status->status === 'capture' ? 'success' : 'warning' }}">
                            Rp {{ number_format($status->total) }}
                        </strong>
                    </div>
                @endforeach

                <hr>

                {{-- By Payment Type --}}
                <h6 class="mb-3">By Payment Type</h6>
                @foreach($financialData['revenue_breakdown']['by_type'] as $type)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-capitalize">{{ str_replace('_', ' ', $type->payment_type) }}</span>
                        <strong class="text-success">Rp {{ number_format($type->total) }}</strong>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Payment Methods Analysis --}}
    <div class="col-md-6 mb-4">
        <div class="card card-round h-100">
            <div class="card-header">
                <h4 class="card-title">Payment Methods Performance</h4>
                <p class="card-category">Successful transactions only</p>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Method</th>
                                <th>Count</th>
                                <th>Total</th>
                                <th>Avg</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($financialData['payment_methods'] as $method)
                                <tr>
                                    <td class="text-capitalize small">{{ str_replace('_', ' ', $method->payment_type) }}</td>
                                    <td class="small">{{ $method->transaction_count }}</td>
                                    <td class="small text-success">Rp {{ number_format($method->total_amount) }}</td>
                                    <td class="small">Rp {{ number_format($method->avg_amount) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No payment data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Monthly Financial Comparison --}}
    <div class="col-md-8 mb-4">
        <div class="card card-round h-100">
            <div class="card-header">
                <h4 class="card-title">Monthly Financial Comparison</h4>
                <p class="card-category">Year {{ $year }} - Revenue & Orders</p>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Revenue</th>
                                <th>Orders</th>
                                <th>Avg Order Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($financialData['monthly_comparison'] as $month)
                                <tr class="{{ $month['month_number'] == date('n') ? 'table-active' : '' }}">
                                    <td class="fw-bold">{{ $month['month'] }}</td>
                                    <td class="text-success">Rp {{ number_format($month['revenue']) }}</td>
                                    <td>{{ number_format($month['orders']) }}</td>
                                    <td class="text-info">Rp {{ number_format($month['avg_order_value']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Profit Analysis --}}
    <div class="col-md-4 mb-4">
        <div class="card card-round h-100">
            <div class="card-header">
                <h4 class="card-title">Profit Analysis</h4>
                <p class="card-category">Estimated figures</p>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h6 class="text-muted">Revenue</h6>
                    <h4 class="text-success mb-0">Rp {{ number_format($financialData['profit_analysis']['total_revenue']) }}</h4>
                </div>

                <div class="mb-4">
                    <h6 class="text-muted">Est. Operational Costs</h6>
                    <h5 class="text-warning mb-0">Rp {{ number_format($financialData['profit_analysis']['estimated_costs']) }}</h5>
                    <small class="text-muted">~30% of revenue</small>
                </div>

                <div class="mb-4">
                    <h6 class="text-muted">Est. Net Profit</h6>
                    <h4 class="text-primary mb-0">Rp {{ number_format($financialData['profit_analysis']['estimated_profit']) }}</h4>
                </div>

                <div class="progress mb-2">
                    <div class="progress-bar bg-success" style="width: {{ $financialData['profit_analysis']['profit_margin'] }}%"></div>
                </div>
                <small class="text-muted">{{ number_format($financialData['profit_analysis']['profit_margin'], 1) }}% profit margin</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Cash Flow Analysis --}}
    <div class="col-md-8 mb-4">
        <div class="card card-round h-100">
            <div class="card-header">
                <h4 class="card-title">Daily Cash Flow</h4>
                <p class="card-category">{{ $financialData['period_info']['date_range'] }}</p>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted">Total Inflow</h6>
                        <h5 class="text-success">Rp {{ number_format($financialData['cash_flow']['total_inflow']) }}</h5>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Avg Daily Inflow</h6>
                        <h5 class="text-info">Rp {{ number_format($financialData['cash_flow']['avg_daily_inflow']) }}</h5>
                    </div>
                </div>

                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Inflow</th>
                                <th>Success</th>
                                <th>Pending</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($financialData['cash_flow']['daily_flow'] as $flow)
                                <tr>
                                    <td class="small">{{ \Carbon\Carbon::parse($flow->date)->format('M d') }}</td>
                                    <td class="small text-success">Rp {{ number_format($flow->inflow) }}</td>
                                    <td class="small">{{ $flow->successful_transactions }}</td>
                                    <td class="small text-warning">{{ $flow->pending_transactions }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No cash flow data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Outstanding Payments --}}
    <div class="col-md-4 mb-4">
        <div class="card card-round h-100">
            <div class="card-header">
                <h4 class="card-title">Outstanding Payments</h4>
                <p class="card-category">Requires attention</p>
            </div>
            <div class="card-body">
                {{-- Pending Payments --}}
                <div class="mb-4">
                    <h6 class="text-warning">Pending Payments</h6>
                    <h5 class="mb-2">Rp {{ number_format($financialData['outstanding_payments']['pending_amount']) }}</h5>

                    @if($financialData['outstanding_payments']['pending']->count() > 0)
                        <div class="small">
                            @foreach($financialData['outstanding_payments']['pending']->take(3) as $payment)
                                <div class="d-flex justify-content-between mb-1">
                                    <span>{{ $payment->order->order_number ?? 'N/A' }}</span>
                                    <span class="text-warning">Rp {{ number_format($payment->gross_amount) }}</span>
                                </div>
                            @endforeach
                            @if($financialData['outstanding_payments']['pending']->count() > 3)
                                <small class="text-muted">+{{ $financialData['outstanding_payments']['pending']->count() - 3 }} more</small>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Failed Payments --}}
                <div>
                    <h6 class="text-danger">Failed Payments</h6>
                    <h5 class="mb-2">Rp {{ number_format($financialData['outstanding_payments']['failed_amount']) }}</h5>

                    @if($financialData['outstanding_payments']['failed']->count() > 0)
                        <div class="small">
                            @foreach($financialData['outstanding_payments']['failed']->take(3) as $payment)
                                <div class="d-flex justify-content-between mb-1">
                                    <span>{{ $payment->order->order_number ?? 'N/A' }}</span>
                                    <span class="text-danger">Rp {{ number_format($payment->gross_amount) }}</span>
                                </div>
                            @endforeach
                            @if($financialData['outstanding_payments']['failed']->count() > 3)
                                <small class="text-muted">+{{ $financialData['outstanding_payments']['failed']->count() - 3 }} more</small>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection