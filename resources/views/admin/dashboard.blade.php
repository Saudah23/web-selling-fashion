@extends('layouts.app')

@section('title', 'Dashboard Admin - Fashion Saazz')


@section('content')
<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    <div>
        <h3 class="fw-bold mb-3">Dashboard Admin</h3>
        <h6 class="op-7 mb-2">Kelola produk dan pesanan untuk toko fashion</h6>
    </div>
</div>

<div class="row">
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
                            <p class="card-category">Total Produk</p>
                            <h4 class="card-title">{{ $totalProducts ?? 0 }}</h4>
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
                        <div class="icon-big text-center icon-warning bubble-shadow-small">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Pesanan Menunggu</p>
                            <h4 class="card-title">{{ $pendingOrders ?? 0 }}</h4>
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
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Pesanan Selesai</p>
                            <h4 class="card-title">{{ $completedOrders ?? 0 }}</h4>
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
                        <div class="icon-big text-center icon-primary bubble-shadow-small">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Pelanggan Aktif</p>
                            <h4 class="card-title">{{ $activeCustomers ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-head-row card-tools-still-right">
                    <h4 class="card-title">Pesanan Terbaru</h4>
                    <div class="card-tools">
                        <button class="btn btn-icon btn-link btn-primary btn-xs">
                            <span class="fa fa-angle-down"></span>
                        </button>
                        <button class="btn btn-icon btn-link btn-primary btn-xs btn-refresh-card">
                            <span class="fa fa-sync-alt"></span>
                        </button>
                        <button class="btn btn-icon btn-link btn-primary btn-xs">
                            <span class="fa fa-times"></span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-items-center mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">ID Pesanan</th>
                                <th scope="col" class="text-end">Pelanggan</th>
                                <th scope="col" class="text-end">Produk</th>
                                <th scope="col" class="text-end">Jumlah</th>
                                <th scope="col" class="text-end">Status</th>
                                <th scope="col" class="text-end">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders ?? [] as $order)
                            <tr>
                                <th scope="row">#{{ $order->order_number }}</th>
                                <td class="text-end">{{ $order->user->name ?? 'N/A' }}</td>
                                <td class="text-end">{{ $order->items->count() }} item</td>
                                <td class="text-end">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                <td class="text-end">
                                    @switch($order->status)
                                        @case('pending')
                                            <span class="badge badge-warning">Menunggu</span>
                                            @break
                                        @case('processing')
                                            <span class="badge badge-info">Diproses</span>
                                            @break
                                        @case('shipped')
                                            <span class="badge badge-primary">Dikirim</span>
                                            @break
                                        @case('delivered')
                                            <span class="badge badge-success">Terkirim</span>
                                            @break
                                        @case('completed')
                                            <span class="badge badge-success">Selesai</span>
                                            @break
                                        @case('cancelled')
                                            <span class="badge badge-danger">Dibatalkan</span>
                                            @break
                                        @default
                                            <span class="badge badge-secondary">{{ $order->status }}</span>
                                    @endswitch
                                </td>
                                <td class="text-end">{{ $order->created_at->format('d/m/Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">Belum ada pesanan terbaru</td>
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