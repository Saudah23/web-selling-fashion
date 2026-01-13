@extends('layouts.app')

@section('title', 'Dashboard Pemilik - Fashion Saazz')

{{-- Owner dashboard now uses owner-sidebar component from layout --}}

@section('content')
<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    <div>
        <h3 class="fw-bold mb-3">Dashboard Pemilik</h3>
        <h6 class="op-7 mb-2">Ringkasan strategis dan wawasan bisnis</h6>
    </div>
    <div class="ms-md-auto py-2 py-md-0">
        <a href="{{ route('owner.settings.index') }}" class="btn btn-primary btn-round">
            <i class="fas fa-cog me-2"></i>
            Pengaturan Sistem
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
                            <p class="card-category">Total Pelanggan</p>
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
                            <p class="card-category">Produk Aktif</p>
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
                            <p class="card-category">Total Pendapatan</p>
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
                            <p class="card-category">Total Pesanan</p>
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
                    <div class="card-title">Ringkasan Bisnis</div>
                    <div class="card-tools">
                        <a href="{{ route('owner.export-report') }}" class="btn btn-label-success btn-round btn-sm me-2">
                            <span class="btn-label">
                                <i class="fa fa-download"></i>
                            </span>
                            Ekspor Laporan
                        </a>
                        <a href="{{ route('owner.analytics') }}" class="btn btn-label-info btn-round btn-sm">
                            <span class="btn-label">
                                <i class="fa fa-chart-line"></i>
                            </span>
                            Lihat Analitik
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Aktivitas Pesanan Terbaru</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>No. Pesanan</th>
                                        <th>Pelanggan</th>
                                        <th>Jumlah</th>
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
                                                @switch($order->status)
                                                    @case('pending')
                                                        <span class="badge badge-warning badge-sm">Menunggu</span>
                                                        @break
                                                    @case('processing')
                                                        <span class="badge badge-info badge-sm">Diproses</span>
                                                        @break
                                                    @case('shipped')
                                                        <span class="badge badge-primary badge-sm">Dikirim</span>
                                                        @break
                                                    @case('delivered')
                                                        <span class="badge badge-success badge-sm">Terkirim</span>
                                                        @break
                                                    @case('completed')
                                                        <span class="badge badge-success badge-sm">Selesai</span>
                                                        @break
                                                    @case('cancelled')
                                                        <span class="badge badge-danger badge-sm">Dibatalkan</span>
                                                        @break
                                                    @default
                                                        <span class="badge badge-secondary badge-sm">{{ $order->status }}</span>
                                                @endswitch
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">Belum ada pesanan terbaru</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Produk Terlaris</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Terjual</th>
                                        <th>Pendapatan</th>
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
                                            <td colspan="3" class="text-center text-muted">Belum ada data penjualan</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <h6 class="text-muted mb-3">Wawasan Cepat</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="icon icon-warning me-3">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">{{ number_format($metrics['pending_orders']) }}</h5>
                                        <p class="text-muted mb-0 small">Pesanan Menunggu</p>
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
                                        <p class="text-muted mb-0 small">Stok Rendah</p>
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
                                        <p class="text-muted mb-0 small">Pelanggan Baru</p>
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
                                        <p class="text-muted mb-0 small">Status Pesanan</p>
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
                    <div class="card-title">Pendapatan {{ $metrics['period_label'] }}</div>
                    <div class="card-tools">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-label-light dropdown-toggle" type="button"
                                    id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                Periode
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item {{ $period === 'current_month' ? 'active' : '' }}"
                                   href="{{ route('owner.dashboard', ['period' => 'current_month']) }}">Bulan Ini</a>
                                <a class="dropdown-item {{ $period === 'last_month' ? 'active' : '' }}"
                                   href="{{ route('owner.dashboard', ['period' => 'last_month']) }}">Bulan Lalu</a>
                                <a class="dropdown-item {{ $period === 'this_year' ? 'active' : '' }}"
                                   href="{{ route('owner.dashboard', ['period' => 'this_year']) }}">Tahun Ini</a>
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
                            <p class="text-muted small mb-3">Pendapatan Harian 7 Hari Terakhir</p>
                            @php
                                $last7Days = [];
                                for($i = 6; $i >= 0; $i--) {
                                    $date = \Carbon\Carbon::now()->subDays($i);
                                    $revenue = collect($revenueTrends['daily'])->where('date', $date->format('M d'))->first()['revenue'] ?? 0;
                                    $last7Days[] = ['date' => $date->format('d M'), 'revenue' => $revenue];
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