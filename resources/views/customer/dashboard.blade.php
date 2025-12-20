@extends('layouts.app')

@section('title', 'Dashboard - FASHION SAAZZ')

@section('content')
    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
        <div>
            <h3 class="fw-bold mb-3">Selamat Datang, {{ auth()->user()->name }}!</h3>
            <h6 class="op-7 mb-2">Kelola pesanan dan temukan fashion terbaru</h6>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6 col-md-3">
            <a href="{{ route('orders.index') }}" class="text-decoration-none">
                <div class="card card-stats card-round">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center icon-primary bubble-shadow-small">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                            </div>
                            <div class="col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Total Pesanan</p>
                                    <h4 class="card-title">{{ $totalOrders }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-md-3">
            <a href="{{ route('orders.index', ['status' => 'pending']) }}" class="text-decoration-none">
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
                                    <p class="card-category">Menunggu</p>
                                    <h4 class="card-title">{{ $pendingOrders }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-md-3">
            <a href="{{ route('wishlist.index') }}" class="text-decoration-none">
                <div class="card card-stats card-round">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center icon-danger bubble-shadow-small">
                                    <i class="fas fa-heart"></i>
                                </div>
                            </div>
                            <div class="col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Wishlist</p>
                                    <h4 class="card-title">{{ $wishlistCount }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-success bubble-shadow-small">
                                <i class="fas fa-wallet"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Total Belanja</p>
                                <h4 class="card-title">Rp {{ number_format($totalSpent, 0, ',', '.') }}</h4>
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
                    <div class="card-head-row">
                        <div class="card-title">Pesanan Terbaru</div>
                        <div class="card-tools">
                            <a href="{{ route('shop') }}" class="btn btn-label-success btn-round btn-sm me-2">
                                <span class="btn-label">
                                    <i class="fa fa-shopping-bag"></i>
                                </span>
                                Belanja
                            </a>
                            <a href="{{ route('orders.index') }}" class="btn btn-label-info btn-round btn-sm">
                                <span class="btn-label">
                                    <i class="fa fa-list"></i>
                                </span>
                                Lihat Semua
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($recentOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">No. Pesanan</th>
                                        <th scope="col">Produk</th>
                                        <th scope="col">Total</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Tanggal</th>
                                        <th scope="col">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentOrders as $order)
                                        <tr>
                                            <td><strong>#{{ $order->order_number }}</strong></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($order->items->first() && $order->items->first()->product && $order->items->first()->product->primaryImage)
                                                        <div class="avatar avatar-sm me-3">
                                                            <img src="{{ asset('storage/' . $order->items->first()->product->primaryImage->image_path) }}"
                                                                alt="..." class="avatar-img rounded">
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <h6 class="mb-0">{{ $order->items->first()->product_name ?? 'Produk' }}</h6>
                                                        @if($order->items->count() > 1)
                                                            <small class="text-muted">+{{ $order->items->count() - 1 }} produk
                                                                lainnya</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                            <td>{!! $order->status_badge !!}</td>
                                            <td>{{ $order->created_at->format('d M Y') }}</td>
                                            <td>
                                                <div class="form-button-action">
                                                    <a href="{{ route('orders.show', $order->order_number) }}"
                                                        class="btn btn-link btn-primary btn-lg" data-bs-toggle="tooltip"
                                                        title="Lihat">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada pesanan</h5>
                            <p class="text-muted">Mulai belanja sekarang!</p>
                            <a href="{{ route('shop') }}" class="btn btn-primary">
                                <i class="fas fa-shopping-bag me-2"></i>Lihat Katalog
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection