@extends('layouts.app')

@section('title', 'Laporan Keuangan - Fashion Saazz')

@section('content')
<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    <div>
        <h3 class="fw-bold mb-3">Laporan Keuangan</h3>
        <h6 class="op-7 mb-2">Ringkasan keuangan toko Anda</h6>
    </div>
    <div class="ms-md-auto py-2 py-md-0">
        <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" id="periodDropdown" data-bs-toggle="dropdown">
                Periode: {{ $period === 'current_month' ? 'Bulan Ini' : ($period === 'last_month' ? 'Bulan Lalu' : 'Tahun Ini') }}
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item {{ $period === 'current_month' ? 'active' : '' }}"
                       href="{{ route('owner.financial-reports', ['period' => 'current_month']) }}">Bulan Ini</a></li>
                <li><a class="dropdown-item {{ $period === 'last_month' ? 'active' : '' }}"
                       href="{{ route('owner.financial-reports', ['period' => 'last_month']) }}">Bulan Lalu</a></li>
                <li><a class="dropdown-item {{ $period === 'this_year' ? 'active' : '' }}"
                       href="{{ route('owner.financial-reports', ['period' => 'this_year']) }}">Tahun Ini</a></li>
            </ul>
        </div>
    </div>
</div>

{{-- Kartu Ringkasan Utama --}}
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card card-round bg-success text-white">
            <div class="card-body text-center py-4">
                <i class="fas fa-wallet fa-2x mb-3"></i>
                <h6 class="mb-2">Total Pendapatan</h6>
                <h3 class="mb-0">Rp {{ number_format($financialData['revenue_breakdown']['total_revenue']) }}</h3>
                <small class="opacity-75">{{ $financialData['period_info']['date_range'] }}</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-round bg-primary text-white">
            <div class="card-body text-center py-4">
                <i class="fas fa-chart-line fa-2x mb-3"></i>
                <h6 class="mb-2">Estimasi Laba Bersih</h6>
                <h3 class="mb-0">Rp {{ number_format($financialData['profit_analysis']['estimated_profit']) }}</h3>
                <small class="opacity-75">{{ number_format($financialData['profit_analysis']['profit_margin'], 1) }}% margin</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-round bg-warning text-dark">
            <div class="card-body text-center py-4">
                <i class="fas fa-clock fa-2x mb-3"></i>
                <h6 class="mb-2">Pembayaran Tertunda</h6>
                <h3 class="mb-0">Rp {{ number_format($financialData['outstanding_payments']['pending_amount']) }}</h3>
                <small>{{ $financialData['outstanding_payments']['pending']->count() }} transaksi</small>
            </div>
        </div>
    </div>
</div>

{{-- Pendapatan Bulanan --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card card-round">
            <div class="card-header">
                <h4 class="card-title">
                    <i class="fas fa-calendar-alt text-primary me-2"></i>
                    Pendapatan per Bulan - Tahun {{ $year }}
                </h4>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($financialData['monthly_comparison'] as $month)
                        <div class="col-md-2 col-6 mb-3">
                            <div class="card border {{ $month['month_number'] == date('n') ? 'border-primary bg-light' : '' }} h-100">
                                <div class="card-body text-center py-3">
                                    <small class="text-muted d-block mb-1">{{ $month['month'] }}</small>
                                    <h6 class="text-success mb-1">Rp {{ number_format($month['revenue'] / 1000) }}K</h6>
                                    <small class="text-muted">{{ $month['orders'] }} pesanan</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Metode Pembayaran & Laba --}}
<div class="row mb-4">
    {{-- Metode Pembayaran --}}
    <div class="col-md-6">
        <div class="card card-round h-100">
            <div class="card-header">
                <h4 class="card-title">
                    <i class="fas fa-credit-card text-info me-2"></i>
                    Metode Pembayaran
                </h4>
            </div>
            <div class="card-body">
                @forelse($financialData['payment_methods'] as $method)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div>
                            <span class="text-capitalize fw-bold">{{ str_replace('_', ' ', $method->payment_type) }}</span>
                            <br>
                            <small class="text-muted">{{ $method->transaction_count }} transaksi</small>
                        </div>
                        <div class="text-end">
                            <span class="text-success fw-bold">Rp {{ number_format($method->total_amount) }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-info-circle fa-2x mb-2"></i>
                        <p class="mb-0">Belum ada data pembayaran</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Estimasi Laba --}}
    <div class="col-md-6">
        <div class="card card-round h-100">
            <div class="card-header">
                <h4 class="card-title">
                    <i class="fas fa-calculator text-success me-2"></i>
                    Estimasi Laba
                </h4>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Pendapatan</span>
                        <span class="text-success fw-bold">Rp {{ number_format($financialData['profit_analysis']['total_revenue']) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Est. Biaya Operasional (30%)</span>
                        <span class="text-danger">- Rp {{ number_format($financialData['profit_analysis']['estimated_costs']) }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">Estimasi Laba Bersih</span>
                        <span class="text-primary fw-bold fs-5">Rp {{ number_format($financialData['profit_analysis']['estimated_profit']) }}</span>
                    </div>
                </div>

                <div class="bg-light rounded p-3 text-center">
                    <small class="text-muted d-block mb-1">Margin Laba</small>
                    <div class="progress mb-2" style="height: 10px;">
                        <div class="progress-bar bg-success" style="width: {{ min($financialData['profit_analysis']['profit_margin'], 100) }}%"></div>
                    </div>
                    <h5 class="text-success mb-0">{{ number_format($financialData['profit_analysis']['profit_margin'], 1) }}%</h5>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Pembayaran Bermasalah (jika ada) --}}
@if($financialData['outstanding_payments']['pending']->count() > 0 || $financialData['outstanding_payments']['failed']->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card card-round border-warning">
            <div class="card-header bg-warning text-dark">
                <h4 class="card-title mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Pembayaran yang Perlu Perhatian
                </h4>
            </div>
            <div class="card-body">
                <div class="row">
                    @if($financialData['outstanding_payments']['pending']->count() > 0)
                    <div class="col-md-6">
                        <h6 class="text-warning mb-3">
                            <i class="fas fa-clock me-1"></i>
                            Tertunda ({{ $financialData['outstanding_payments']['pending']->count() }})
                        </h6>
                        @foreach($financialData['outstanding_payments']['pending']->take(5) as $payment)
                            <div class="d-flex justify-content-between py-2 border-bottom">
                                <span>{{ $payment->order->order_number ?? 'N/A' }}</span>
                                <span class="text-warning fw-bold">Rp {{ number_format($payment->gross_amount) }}</span>
                            </div>
                        @endforeach
                    </div>
                    @endif

                    @if($financialData['outstanding_payments']['failed']->count() > 0)
                    <div class="col-md-6">
                        <h6 class="text-danger mb-3">
                            <i class="fas fa-times-circle me-1"></i>
                            Gagal ({{ $financialData['outstanding_payments']['failed']->count() }})
                        </h6>
                        @foreach($financialData['outstanding_payments']['failed']->take(5) as $payment)
                            <div class="d-flex justify-content-between py-2 border-bottom">
                                <span>{{ $payment->order->order_number ?? 'N/A' }}</span>
                                <span class="text-danger fw-bold">Rp {{ number_format($payment->gross_amount) }}</span>
                            </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('styles')
<style>
.card-round {
    border-radius: 10px;
}
.opacity-75 {
    opacity: 0.75;
}
</style>
@endpush