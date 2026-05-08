@extends('layouts.app')

@section('title', 'Ringkasan Penjualan - Dashboard Pemilik')

@section('content')
<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    <div>
        <h3 class="fw-bold mb-3">Ringkasan Penjualan</h3>
        <h6 class="op-7 mb-2">Lihat performa penjualan toko Anda</h6>
    </div>
    <div class="ms-md-auto py-2 py-md-0">
        <a href="{{ route('owner.dashboard') }}" class="btn btn-secondary btn-round me-2">
            <i class="fas fa-arrow-left me-2"></i>
            Kembali ke Dashboard
        </a>
        <a href="{{ route('owner.export-report') }}?format=excel" class="btn btn-success btn-round">
            <i class="fas fa-download me-2"></i>
            Ekspor Data
        </a>
    </div>
</div>

<!-- Wawasan & Peringatan Bisnis -->
@if(count($insights) > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card card-round">
            <div class="card-header">
                <h4 class="card-title">
                    <i class="fas fa-lightbulb text-warning me-2"></i>
                    Saran untuk Toko Anda
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
                                    <strong>Tindakan:</strong> {{ $insight['action'] }}
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

<!-- Pendapatan Harian -->
<div class="row mb-4">
    <div class="col-lg-6">
        <div class="card card-round h-100">
            <div class="card-header">
                <h4 class="card-title">
                    <i class="fas fa-calendar-week text-primary me-2"></i>
                    Pendapatan Minggu Ini
                </h4>
            </div>
            <div class="card-body">
                @php $totalWeekRevenue = $advancedAnalytics['revenue_analysis']['by_day_of_week']->sum('revenue'); @endphp
                @foreach($advancedAnalytics['revenue_analysis']['by_day_of_week'] as $day)
                    @php $percentage = $totalWeekRevenue > 0 ? ($day['revenue'] / $totalWeekRevenue) * 100 : 0; @endphp
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <div>
                            <h6 class="mb-1">{{ $day['day'] }}</h6>
                            <div class="progress" style="height: 5px; width: 100px;">
                                <div class="progress-bar bg-primary" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                        <div class="text-end">
                            <strong class="text-primary">Rp {{ number_format($day['revenue']) }}</strong>
                        </div>
                    </div>
                @endforeach
                <div class="mt-3 p-3 bg-light rounded text-center">
                    <h6 class="text-muted mb-1">Total Minggu Ini</h6>
                    <h4 class="text-success mb-0">Rp {{ number_format($totalWeekRevenue) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Produk Terlaris -->
    <div class="col-lg-6">
        <div class="card card-round h-100">
            <div class="card-header">
                <h4 class="card-title">
                    <i class="fas fa-fire text-danger me-2"></i>
                    Produk Terlaris
                </h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th class="text-end">Terjual/Hari</th>
                                <th class="text-end">Total Terjual</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($advancedAnalytics['product_performance']['velocity'] as $product)
                                <tr>
                                    <td>{{ Str::limit($product['name'], 25) }}</td>
                                    <td class="text-end">
                                        <span class="badge badge-{{ $product['velocity'] > 1 ? 'success' : 'warning' }}">
                                            {{ $product['velocity'] }}/hari
                                        </span>
                                    </td>
                                    <td class="text-end"><strong>{{ $product['total_sold'] }}</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistik Pelanggan Sederhana -->
<div class="row mb-4">
    <div class="col-lg-4">
        <div class="card card-round">
            <div class="card-body text-center py-4">
                <div class="mb-3">
                    <i class="fas fa-users fa-3x text-primary"></i>
                </div>
                <h6 class="text-muted">Total Pendaftaran</h6>
                <h3 class="mb-0">{{ number_format($advancedAnalytics['conversion_funnel']['registrations']) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card card-round">
            <div class="card-body text-center py-4">
                <div class="mb-3">
                    <i class="fas fa-shopping-cart fa-3x text-success"></i>
                </div>
                <h6 class="text-muted">Pesanan Selesai</h6>
                <h3 class="mb-0">{{ number_format($advancedAnalytics['conversion_funnel']['completed_orders']) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card card-round">
            <div class="card-body text-center py-4">
                <div class="mb-3">
                    <i class="fas fa-percentage fa-3x text-warning"></i>
                </div>
                <h6 class="text-muted">Tingkat Penyelesaian</h6>
                <h3 class="mb-0">{{ number_format($advancedAnalytics['conversion_funnel']['order_completion_rate'], 1) }}%</h3>
            </div>
        </div>
    </div>
</div>

<!-- Tren Pendapatan Bulanan (Sederhana) -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card card-round">
            <div class="card-header">
                <h4 class="card-title">
                    <i class="fas fa-chart-line text-info me-2"></i>
                    Pendapatan 6 Bulan Terakhir
                </h4>
            </div>
            <div class="card-body">
                <div class="row">
                    @php $recentMonths = array_slice(is_array($analyticsData['revenue']['monthly']) ? $analyticsData['revenue']['monthly'] : $analyticsData['revenue']['monthly']->toArray(), -6); @endphp
                    @foreach($recentMonths as $index => $month)
                        <div class="col-md-2 col-6 mb-3">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body text-center py-3">
                                    <small class="text-muted d-block mb-2">{{ $month['month'] }}</small>
                                    <h6 class="text-primary mb-0">Rp {{ number_format($month['revenue'] / 1000) }}K</h6>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.card-round {
    border-radius: 10px;
}
.alert {
    border-radius: 8px;
}
.table th {
    font-weight: 600;
    font-size: 0.85rem;
    border-top: none;
}
.table td {
    vertical-align: middle;
}
.badge {
    font-weight: 500;
}
</style>
@endpush