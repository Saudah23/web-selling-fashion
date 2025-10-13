@extends('layouts.app')

@section('title', 'Customer Dashboard - Fashion Marketplace')

@section('sidebar')
<li class="nav-item">
    <a href="{{ route('customer.dashboard') }}" class="nav-link">
        <i class="fas fa-home"></i>
        <p>Dashboard</p>
    </a>
</li>
<li class="nav-item">
    <a href="#">
        <i class="fas fa-shopping-bag"></i>
        <p>Browse Products</p>
    </a>
</li>
<li class="nav-item">
    <a data-bs-toggle="collapse" href="#orders">
        <i class="fas fa-shopping-cart"></i>
        <p>My Orders</p>
        <span class="caret"></span>
    </a>
    <div class="collapse" id="orders">
        <ul class="nav nav-collapse">
            <li><a href="{{ route('orders.index') }}"><span class="sub-item">All Orders</span></a></li>
            <li><a href="{{ route('orders.index', ['status' => 'pending']) }}"><span class="sub-item">Pending</span></a></li>
            <li><a href="{{ route('orders.index', ['status' => 'delivered']) }}"><span class="sub-item">Completed</span></a></li>
        </ul>
    </div>
</li>
<li class="nav-item">
    <a href="{{ route('shop') }}?wishlist=1">
        <i class="fas fa-heart"></i>
        <p>Wishlist</p>
    </a>
</li>
<li class="nav-item">
    <a href="#">
        <i class="fas fa-credit-card"></i>
        <p>Payment Methods</p>
    </a>
</li>
@endsection

@section('content')
<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    <div>
        <h3 class="fw-bold mb-3">Welcome back, {{ auth()->user()->name }}!</h3>
        <h6 class="op-7 mb-2">Discover the latest fashion trends and manage your orders</h6>
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
                                <p class="card-category">Total Orders</p>
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
                                <p class="card-category">Pending Orders</p>
                                <h4 class="card-title">{{ $pendingOrders }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-md-3">
        <a href="{{ route('shop') }}?wishlist=1" class="text-decoration-none">
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
                                <p class="card-category">Wishlist Items</p>
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
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Total Spent</p>
                            <h4 class="card-title">$1,245</h4>
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
                    <div class="card-title">Recent Orders</div>
                    <div class="card-tools">
                        <a href="#" class="btn btn-label-success btn-round btn-sm me-2">
                            <span class="btn-label">
                                <i class="fa fa-plus"></i>
                            </span>
                            New Order
                        </a>
                        <a href="#" class="btn btn-label-info btn-round btn-sm">
                            <span class="btn-label">
                                <i class="fa fa-list"></i>
                            </span>
                            View All
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-items-center mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">Order ID</th>
                                <th scope="col">Product</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Status</th>
                                <th scope="col">Date</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#ORD-045</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            <img src="{{ asset('kaiadmin-lite-1.2.0/assets/img/products/product1.jpg') }}" alt="..." class="avatar-img rounded">
                                        </div>
                                        <div>
                                            <h6 class="mb-0">Premium T-Shirt</h6>
                                            <small class="text-muted">Size: L, Color: Black</small>
                                        </div>
                                    </div>
                                </td>
                                <td>$29.99</td>
                                <td><span class="badge badge-success">Delivered</span></td>
                                <td>2024-03-10</td>
                                <td>
                                    <div class="form-button-action">
                                        <button type="button" class="btn btn-link btn-primary btn-lg" data-bs-toggle="tooltip" title="View">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-link btn-danger" data-bs-toggle="tooltip" title="Return">
                                            <i class="fa fa-undo"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>#ORD-046</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            <img src="{{ asset('kaiadmin-lite-1.2.0/assets/img/products/product2.jpg') }}" alt="..." class="avatar-img rounded">
                                        </div>
                                        <div>
                                            <h6 class="mb-0">Denim Jacket</h6>
                                            <small class="text-muted">Size: M, Color: Blue</small>
                                        </div>
                                    </div>
                                </td>
                                <td>$89.99</td>
                                <td><span class="badge badge-warning">Processing</span></td>
                                <td>2024-03-12</td>
                                <td>
                                    <div class="form-button-action">
                                        <button type="button" class="btn btn-link btn-primary btn-lg" data-bs-toggle="tooltip" title="View">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-link btn-danger" data-bs-toggle="tooltip" title="Cancel">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">Recommended Products</div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6 col-sm-4 col-lg-12 col-xl-6">
                        <div class="card">
                            <div class="card-body p-2">
                                <img src="{{ asset('kaiadmin-lite-1.2.0/assets/img/products/product3.jpg') }}"
                                     class="card-img-top" alt="Product" style="height: 120px; object-fit: cover;">
                                <div class="mt-2">
                                    <h6 class="card-title mb-1">Summer Dress</h6>
                                    <p class="card-text text-success fw-bold">$45.00</p>
                                    <button class="btn btn-primary btn-sm w-100">Add to Cart</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-4 col-lg-12 col-xl-6">
                        <div class="card">
                            <div class="card-body p-2">
                                <img src="{{ asset('kaiadmin-lite-1.2.0/assets/img/products/product4.jpg') }}"
                                     class="card-img-top" alt="Product" style="height: 120px; object-fit: cover;">
                                <div class="mt-2">
                                    <h6 class="card-title mb-1">Casual Sneakers</h6>
                                    <p class="card-text text-success fw-bold">$75.00</p>
                                    <button class="btn btn-primary btn-sm w-100">Add to Cart</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection