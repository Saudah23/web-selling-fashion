@extends('layouts.app')

@section('title', 'Admin Dashboard - Fashion Marketplace')


@section('content')
<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    <div>
        <h3 class="fw-bold mb-3">Admin Dashboard</h3>
        <h6 class="op-7 mb-2">Manage products and orders for the fashion marketplace</h6>
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
                            <p class="card-category">Total Products</p>
                            <h4 class="card-title">1,303</h4>
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
                            <p class="card-category">Pending Orders</p>
                            <h4 class="card-title">24</h4>
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
                            <p class="card-category">Completed Orders</p>
                            <h4 class="card-title">552</h4>
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
                            <p class="card-category">Active Customers</p>
                            <h4 class="card-title">1,294</h4>
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
                    <h4 class="card-title">Recent Orders</h4>
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
                                <th scope="col">Order ID</th>
                                <th scope="col" class="text-end">Customer</th>
                                <th scope="col" class="text-end">Product</th>
                                <th scope="col" class="text-end">Amount</th>
                                <th scope="col" class="text-end">Status</th>
                                <th scope="col" class="text-end">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">#ORD-001</th>
                                <td class="text-end">John Doe</td>
                                <td class="text-end">Premium T-Shirt</td>
                                <td class="text-end">$29.99</td>
                                <td class="text-end">
                                    <span class="badge badge-success">Completed</span>
                                </td>
                                <td class="text-end">2024-03-15</td>
                            </tr>
                            <tr>
                                <th scope="row">#ORD-002</th>
                                <td class="text-end">Jane Smith</td>
                                <td class="text-end">Denim Jacket</td>
                                <td class="text-end">$89.99</td>
                                <td class="text-end">
                                    <span class="badge badge-warning">Processing</span>
                                </td>
                                <td class="text-end">2024-03-14</td>
                            </tr>
                            <tr>
                                <th scope="row">#ORD-003</th>
                                <td class="text-end">Mike Johnson</td>
                                <td class="text-end">Casual Pants</td>
                                <td class="text-end">$59.99</td>
                                <td class="text-end">
                                    <span class="badge badge-danger">Pending</span>
                                </td>
                                <td class="text-end">2024-03-13</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection