@extends('layouts.app')

@section('title', 'Laporan Bisnis - Fashion Saazz')

@section('content')
<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    <div>
        <h3 class="fw-bold mb-3">Laporan Bisnis</h3>
        <h6 class="op-7 mb-2">Intelijen bisnis dan analisis performa komprehensif</h6>
    </div>
    <div class="ms-md-auto py-2 py-md-0">
        <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" id="periodDropdown" data-bs-toggle="dropdown">
                Periode: {{ $period === 'current_month' ? 'Bulan Ini' : ($period === 'last_month' ? 'Bulan Lalu' : 'Tahun Ini') }}
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item {{ $period === 'current_month' ? 'active' : '' }}"
                       href="{{ route('owner.business-reports', ['period' => 'current_month']) }}">Bulan Ini</a></li>
                <li><a class="dropdown-item {{ $period === 'last_month' ? 'active' : '' }}"
                       href="{{ route('owner.business-reports', ['period' => 'last_month']) }}">Bulan Lalu</a></li>
                <li><a class="dropdown-item {{ $period === 'this_year' ? 'active' : '' }}"
                       href="{{ route('owner.business-reports', ['period' => 'this_year']) }}">Tahun Ini</a></li>
            </ul>
        </div>
    </div>
</div>

{{-- Ringkasan Eksekutif --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card card-round">
            <div class="card-header">
                <h4 class="card-title">Ringkasan Eksekutif</h4>
                <p class="card-category">{{ $businessData['period_info']['date_range'] }}</p>
            </div>
            <div class="card-body">
                {{-- Ringkasan KPI --}}
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="text-center">
                            <h5 class="text-success">Rp {{ number_format($businessData['executive_summary']['kpi_summary']['revenue']) }}</h5>
                            <small class="text-muted">Total Pendapatan</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h5 class="text-info">{{ number_format($businessData['executive_summary']['kpi_summary']['orders']) }}</h5>
                            <small class="text-muted">Total Pesanan</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h5 class="text-primary">{{ number_format($businessData['executive_summary']['kpi_summary']['customers']) }}</h5>
                            <small class="text-muted">Total Pelanggan</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h5 class="text-warning">Rp {{ number_format($businessData['executive_summary']['kpi_summary']['aov']) }}</h5>
                            <small class="text-muted">Nilai Pesanan Rata-rata</small>
                        </div>
                    </div>
                </div>

                {{-- Wawasan Utama --}}
                @if(count($businessData['executive_summary']['insights']) > 0)
                    <h6 class="mb-3">Wawasan Bisnis Utama</h6>
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
    {{-- Metrik Bisnis --}}
    <div class="col-md-4 mb-4">
        <div class="card card-round h-100">
            <div class="card-header">
                <h4 class="card-title">Metrik Bisnis</h4>
                <p class="card-category">Indikator performa utama</p>
            </div>
            <div class="card-body">
                {{-- Metrik Pendapatan --}}
                <div class="mb-4">
                    <h6 class="text-muted">Performa Pendapatan</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Pendapatan</span>
                        <strong class="text-success">Rp {{ number_format($businessData['business_metrics']['revenue']['total']) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Pesanan</span>
                        <strong>{{ number_format($businessData['business_metrics']['revenue']['orders']) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Nilai Pesanan Rata-rata</span>
                        <strong class="text-warning">Rp {{ number_format($businessData['business_metrics']['revenue']['avg_order_value']) }}</strong>
                    </div>
                </div>

                {{-- Metrik Pelanggan --}}
                <div class="mb-4">
                    <h6 class="text-muted">Performa Pelanggan</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Pelanggan</span>
                        <strong>{{ number_format($businessData['business_metrics']['customers']['total']) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Pelanggan Baru</span>
                        <strong class="text-info">{{ number_format($businessData['business_metrics']['customers']['new']) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Tingkat Akuisisi</span>
                        <strong class="text-{{ $businessData['business_metrics']['customers']['acquisition_rate'] > 10 ? 'success' : 'warning' }}">
                            {{ number_format($businessData['business_metrics']['customers']['acquisition_rate'], 1) }}%
                        </strong>
                    </div>
                </div>

                {{-- Metrik Produk --}}
                <div>
                    <h6 class="text-muted">Performa Produk</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Produk</span>
                        <strong>{{ number_format($businessData['business_metrics']['products']['total']) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Produk Terjual</span>
                        <strong>{{ number_format($businessData['business_metrics']['products']['sold']) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Tingkat Penjualan</span>
                        <strong class="text-{{ $businessData['business_metrics']['products']['sell_through_rate'] > 50 ? 'success' : 'warning' }}">
                            {{ number_format($businessData['business_metrics']['products']['sell_through_rate'], 1) }}%
                        </strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Analitik Pelanggan --}}
    <div class="col-md-4 mb-4">
        <div class="card card-round h-100">
            <div class="card-header">
                <h4 class="card-title">Analitik Pelanggan</h4>
                <p class="card-category">Wawasan perilaku pelanggan</p>
            </div>
            <div class="card-body">
                {{-- Segmentasi Pelanggan --}}
                <div class="mb-4">
                    <h6 class="text-muted">Segmentasi Pelanggan</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tanpa Pesanan</span>
                        <strong>{{ number_format($businessData['customer_analytics']['segments']->no_orders ?? 0) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Pesanan Tunggal</span>
                        <strong>{{ number_format($businessData['customer_analytics']['segments']->single_order ?? 0) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Reguler (2-5 pesanan)</span>
                        <strong class="text-info">{{ number_format($businessData['customer_analytics']['segments']->regular ?? 0) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Loyal (5+ pesanan)</span>
                        <strong class="text-success">{{ number_format($businessData['customer_analytics']['segments']->loyal ?? 0) }}</strong>
                    </div>
                </div>

                {{-- Pelanggan Teratas --}}
                <div class="mb-4">
                    <h6 class="text-muted">Pelanggan Teratas (berdasarkan Pendapatan)</h6>
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
                            <p class="text-muted small">Tidak ada data pelanggan</p>
                        @endforelse
                    </div>
                </div>

                {{-- Distribusi Geografis --}}
                <div>
                    <h6 class="text-muted">Distribusi Geografis</h6>
                    <div style="max-height: 150px; overflow-y: auto;">
                        @forelse($businessData['customer_analytics']['geographic_distribution']->take(5) as $geo)
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small">{{ $geo->province_name }}</span>
                                <strong class="small">{{ $geo->customer_count }}</strong>
                            </div>
                        @empty
                            <p class="text-muted small">Tidak ada data geografis</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Metrik Pertumbuhan --}}
    <div class="col-md-4 mb-4">
        <div class="card card-round h-100">
            <div class="card-header">
                <h4 class="card-title">Analisis Pertumbuhan</h4>
                <p class="card-category">Perbandingan periode ke periode</p>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h6 class="text-muted">Pertumbuhan Pendapatan</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Periode Saat Ini</span>
                        <strong class="text-success">Rp {{ number_format($businessData['growth_metrics']['current']['revenue']) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Periode Sebelumnya</span>
                        <strong class="text-muted">Rp {{ number_format($businessData['growth_metrics']['previous']['revenue']) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Tingkat Pertumbuhan</span>
                        <strong class="text-{{ $businessData['growth_metrics']['growth']['revenue'] >= 0 ? 'success' : 'danger' }}">
                            {{ $businessData['growth_metrics']['growth']['revenue'] >= 0 ? '+' : '' }}{{ number_format($businessData['growth_metrics']['growth']['revenue'], 1) }}%
                        </strong>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="text-muted">Pertumbuhan Pesanan</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Periode Saat Ini</span>
                        <strong class="text-info">{{ number_format($businessData['growth_metrics']['current']['orders']) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Periode Sebelumnya</span>
                        <strong class="text-muted">{{ number_format($businessData['growth_metrics']['previous']['orders']) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Tingkat Pertumbuhan</span>
                        <strong class="text-{{ $businessData['growth_metrics']['growth']['orders'] >= 0 ? 'success' : 'danger' }}">
                            {{ $businessData['growth_metrics']['growth']['orders'] >= 0 ? '+' : '' }}{{ number_format($businessData['growth_metrics']['growth']['orders'], 1) }}%
                        </strong>
                    </div>
                </div>

                <div class="progress mb-2">
                    <div class="progress-bar bg-{{ $businessData['growth_metrics']['growth']['revenue'] >= 0 ? 'success' : 'danger' }}"
                         style="width: {{ min(abs($businessData['growth_metrics']['growth']['revenue']), 100) }}%"></div>
                </div>
                <small class="text-muted">Indikator pertumbuhan pendapatan</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Performa Produk --}}
    <div class="col-md-6 mb-4">
        <div class="card card-round h-100">
            <div class="card-header">
                <h4 class="card-title">Performa Produk</h4>
                <p class="card-category">Produk terlaris dan analisis kategori</p>
            </div>
            <div class="card-body">
                {{-- Produk Terlaris --}}
                <div class="mb-4">
                    <h6 class="text-muted">Produk Terlaris</h6>
                    <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Terjual</th>
                                    <th>Pendapatan</th>
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
                                        <td colspan="3" class="text-center text-muted">Tidak ada data penjualan produk</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Performa Kategori --}}
                <div>
                    <h6 class="text-muted">Performa Kategori</h6>
                    <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Kategori</th>
                                    <th>Produk</th>
                                    <th>Pendapatan</th>
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
                                        <td colspan="3" class="text-center text-muted">Tidak ada data kategori</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Analitik Pesanan --}}
    <div class="col-md-6 mb-4">
        <div class="card card-round h-100">
            <div class="card-header">
                <h4 class="card-title">Analitik Pesanan</h4>
                <p class="card-category">Wawasan pemenuhan pesanan</p>
            </div>
            <div class="card-body">
                {{-- Distribusi Status Pesanan --}}
                <div class="mb-4">
                    <h6 class="text-muted">Distribusi Status Pesanan</h6>
                    @foreach($businessData['order_analytics']['status_distribution'] as $status)
                        <div class="d-flex justify-content-between mb-2">
                            @switch($status->status)
                                @case('pending')
                                    <span>Menunggu</span>
                                    @break
                                @case('processing')
                                    <span>Diproses</span>
                                    @break
                                @case('shipped')
                                    <span>Dikirim</span>
                                    @break
                                @case('delivered')
                                    <span>Terkirim</span>
                                    @break
                                @case('completed')
                                    <span>Selesai</span>
                                    @break
                                @case('cancelled')
                                    <span>Dibatalkan</span>
                                    @break
                                @default
                                    <span class="text-capitalize">{{ $status->status }}</span>
                            @endswitch
                            <strong class="badge badge-{{ $status->status === 'delivered' || $status->status === 'completed' ? 'success' : ($status->status === 'pending' ? 'warning' : ($status->status === 'cancelled' ? 'danger' : 'info')) }}">
                                {{ $status->count }}
                            </strong>
                        </div>
                    @endforeach
                </div>

                {{-- Metrik Pemenuhan --}}
                <div class="mb-4">
                    <h6 class="text-muted">Metrik Pemenuhan</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Pesanan</span>
                        <strong>{{ number_format($businessData['order_analytics']['fulfillment_metrics']['total_orders']) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Selesai</span>
                        <strong class="text-success">{{ number_format($businessData['order_analytics']['fulfillment_metrics']['completed_orders']) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tertunda</span>
                        <strong class="text-warning">{{ number_format($businessData['order_analytics']['fulfillment_metrics']['pending_orders']) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Dibatalkan</span>
                        <strong class="text-danger">{{ number_format($businessData['order_analytics']['fulfillment_metrics']['cancelled_orders']) }}</strong>
                    </div>
                </div>

                {{-- Waktu Pemrosesan --}}
                <div>
                    <h6 class="text-muted">Waktu Pemrosesan Rata-rata</h6>
                    <h4 class="text-info">{{ number_format($businessData['order_analytics']['avg_processing_time'], 1) }} jam</h4>
                    <small class="text-muted">Dari pesanan ke pengiriman</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Wawasan Pasar --}}
    <div class="col-md-6 mb-4">
        <div class="card card-round h-100">
            <div class="card-header">
                <h4 class="card-title">Wawasan Pasar</h4>
                <p class="card-category">Pola perilaku pelanggan</p>
            </div>
            <div class="card-body">
                {{-- Jam Puncak Pesanan --}}
                <div class="mb-4">
                    <h6 class="text-muted">Jam Puncak Pesanan</h6>
                    @foreach($businessData['market_insights']['peak_times'] as $time)
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ $time->hour }}:00 - {{ $time->hour + 1 }}:00</span>
                            <strong class="text-info">{{ $time->order_count }} pesanan</strong>
                        </div>
                    @endforeach
                </div>

                {{-- Hari Puncak Pesanan --}}
                <div>
                    <h6 class="text-muted">Hari Puncak Pesanan</h6>
                    @foreach($businessData['market_insights']['peak_days'] as $day)
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ $day->day_name }}</span>
                            <strong class="text-success">{{ $day->order_count }} pesanan</strong>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Peringatan Stok Rendah --}}
    <div class="col-md-6 mb-4">
        <div class="card card-round h-100">
            <div class="card-header">
                <h4 class="card-title">Peringatan Inventaris</h4>
                <p class="card-category">Peringatan stok rendah</p>
            </div>
            <div class="card-body">
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Stok</th>
                                <th>Harga</th>
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
                                            {{ $product->stock_quantity <= 5 ? 'Kritis' : 'Rendah' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Semua produk memiliki stok memadai</td>
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