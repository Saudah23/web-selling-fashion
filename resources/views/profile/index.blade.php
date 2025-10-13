@extends('layouts.app')

@section('title', 'My Profile - Fashion Marketplace')

@section('sidebar')
@php
    $role = auth()->user()->role;
@endphp

@if($role === 'owner')
    <li class="nav-item">
        <a href="{{ route('owner.dashboard') }}" class="nav-link">
            <i class="fas fa-home"></i>
            <p>Dashboard</p>
        </a>
    </li>
    <li class="nav-item">
        <a data-bs-toggle="collapse" href="#products">
            <i class="fas fa-tshirt"></i>
            <p>Product Management</p>
            <span class="caret"></span>
        </a>
        <div class="collapse" id="products">
            <ul class="nav nav-collapse">
                <li><a href="#"><span class="sub-item">All Products</span></a></li>
                <li><a href="#"><span class="sub-item">Add Product</span></a></li>
                <li><a href="#"><span class="sub-item">Categories</span></a></li>
            </ul>
        </div>
    </li>
    <li class="nav-item">
        <a data-bs-toggle="collapse" href="#users">
            <i class="fas fa-users"></i>
            <p>User Management</p>
            <span class="caret"></span>
        </a>
        <div class="collapse" id="users">
            <ul class="nav nav-collapse">
                <li><a href="#"><span class="sub-item">All Users</span></a></li>
                <li><a href="#"><span class="sub-item">Admins</span></a></li>
                <li><a href="#"><span class="sub-item">Customers</span></a></li>
            </ul>
        </div>
    </li>
    <li class="nav-item">
        <a href="#">
            <i class="fas fa-chart-bar"></i>
            <p>Reports</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="#">
            <i class="fas fa-cog"></i>
            <p>Settings</p>
        </a>
    </li>
@elseif($role === 'admin')
    <li class="nav-item">
        <a href="{{ route('admin.dashboard') }}" class="nav-link">
            <i class="fas fa-home"></i>
            <p>Dashboard</p>
        </a>
    </li>
    <li class="nav-item">
        <a data-bs-toggle="collapse" href="#products">
            <i class="fas fa-tshirt"></i>
            <p>Product Management</p>
            <span class="caret"></span>
        </a>
        <div class="collapse" id="products">
            <ul class="nav nav-collapse">
                <li><a href="#"><span class="sub-item">All Products</span></a></li>
                <li><a href="#"><span class="sub-item">Add Product</span></a></li>
                <li><a href="#"><span class="sub-item">Categories</span></a></li>
            </ul>
        </div>
    </li>
    <li class="nav-item">
        <a data-bs-toggle="collapse" href="#orders">
            <i class="fas fa-shopping-cart"></i>
            <p>Order Management</p>
            <span class="caret"></span>
        </a>
        <div class="collapse" id="orders">
            <ul class="nav nav-collapse">
                <li><a href="#"><span class="sub-item">All Orders</span></a></li>
                <li><a href="#"><span class="sub-item">Pending</span></a></li>
                <li><a href="#"><span class="sub-item">Completed</span></a></li>
            </ul>
        </div>
    </li>
    <li class="nav-item">
        <a href="#">
            <i class="fas fa-users"></i>
            <p>Customer Management</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="#">
            <i class="fas fa-chart-line"></i>
            <p>Analytics</p>
        </a>
    </li>
@else
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
                <li><a href="#"><span class="sub-item">All Orders</span></a></li>
                <li><a href="#"><span class="sub-item">Pending</span></a></li>
                <li><a href="#"><span class="sub-item">Completed</span></a></li>
            </ul>
        </div>
    </li>
    <li class="nav-item">
        <a href="#">
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
@endif

@endsection

@section('content')
<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    <div>
        <h3 class="fw-bold mb-3">My Profile</h3>
        <h6 class="op-7 mb-2">Manage your account information and security</h6>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">Profile Management</div>
                </div>
                <div class="card-category">
                    <nav class="nav nav-pills nav-justified" id="profile-tabs" role="tablist">
                        <button class="nav-link active" id="details-tab" data-bs-toggle="pill" data-bs-target="#details"
                                type="button" role="tab" aria-controls="details" aria-selected="true">
                            <i class="fas fa-user me-2"></i>Profile Details
                        </button>
                        <button class="nav-link" id="edit-tab" data-bs-toggle="pill" data-bs-target="#edit"
                                type="button" role="tab" aria-controls="edit" aria-selected="false">
                            <i class="fas fa-edit me-2"></i>Edit Profile
                        </button>
                        <button class="nav-link" id="password-tab" data-bs-toggle="pill" data-bs-target="#password"
                                type="button" role="tab" aria-controls="password" aria-selected="false">
                            <i class="fas fa-lock me-2"></i>Change Password
                        </button>
                    </nav>
                </div>
            </div>
            <div class="card-body">
                <div class="tab-content" id="profile-tab-content">

                    <!-- Profile Details Tab -->
                    <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <div class="avatar avatar-xxl mb-4">
                                    <img src="{{ asset('kaiadmin-lite-1.2.0/assets/img/profile.jpg') }}"
                                         alt="Profile Picture" class="avatar-img rounded-circle">
                                </div>
                                <div class="role-badge role-{{ $user->role }} mb-3">
                                    {{ ucfirst($user->role) }}
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="profile-info">
                                    <div class="info-item mb-4">
                                        <label class="fw-bold text-muted">Full Name</label>
                                        <h5 class="mb-0">{{ $user->name }}</h5>
                                    </div>
                                    <div class="info-item mb-4">
                                        <label class="fw-bold text-muted">Email Address</label>
                                        <h5 class="mb-0">{{ $user->email }}</h5>
                                    </div>
                                    <div class="info-item mb-4">
                                        <label class="fw-bold text-muted">Account Type</label>
                                        <h5 class="mb-0">
                                            <span class="role-badge role-{{ $user->role }}">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </h5>
                                    </div>
                                    <div class="info-item mb-4">
                                        <label class="fw-bold text-muted">Member Since</label>
                                        <h5 class="mb-0">{{ $user->created_at->format('F j, Y') }}</h5>
                                    </div>
                                    <div class="info-item mb-4">
                                        <label class="fw-bold text-muted">Email Status</label>
                                        <h5 class="mb-0">
                                            @if($user->email_verified_at)
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check-circle me-1"></i>Verified
                                                </span>
                                            @else
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-exclamation-circle me-1"></i>Not Verified
                                                </span>
                                            @endif
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Profile Tab -->
                    <div class="tab-pane fade" id="edit" role="tabpanel" aria-labelledby="edit-tab">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('profile.update') }}">
                            @csrf
                            @method('PATCH')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name" class="form-label">Full Name</label>
                                        <input type="text"
                                               class="form-control @error('name') is-invalid @enderror"
                                               id="name"
                                               name="name"
                                               value="{{ old('name', $user->name) }}"
                                               required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email"
                                               class="form-control @error('email') is-invalid @enderror"
                                               id="email"
                                               name="email"
                                               value="{{ old('email', $user->email) }}"
                                               required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Account Type</label>
                                <div class="form-control-static">
                                    <span class="role-badge role-{{ $user->role }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                    <small class="form-text text-muted">Account type cannot be changed</small>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Profile
                                </button>
                                <button type="button" class="btn btn-secondary ms-2" onclick="resetForm()">
                                    <i class="fas fa-undo me-2"></i>Reset
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Change Password Tab -->
                    <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                        @if(session('password_success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>{{ session('password_success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('profile.password') }}">
                            @csrf
                            @method('PATCH')

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="current_password" class="form-label">Current Password</label>
                                        <input type="password"
                                               class="form-control @error('current_password') is-invalid @enderror"
                                               id="current_password"
                                               name="current_password"
                                               required>
                                        @error('current_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password" class="form-label">New Password</label>
                                        <input type="password"
                                               class="form-control @error('password') is-invalid @enderror"
                                               id="password"
                                               name="password"
                                               required>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                        <input type="password"
                                               class="form-control"
                                               id="password_confirmation"
                                               name="password_confirmation"
                                               required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-key me-2"></i>Update Password
                                </button>
                                <button type="button" class="btn btn-secondary ms-2" onclick="clearPasswordForm()">
                                    <i class="fas fa-times me-2"></i>Clear
                                </button>
                            </div>

                            <div class="alert alert-info mt-3" role="alert">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Password Requirements:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Minimum 8 characters long</li>
                                    <li>Must contain at least one uppercase letter</li>
                                    <li>Must contain at least one lowercase letter</li>
                                    <li>Must contain at least one number</li>
                                </ul>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Show success notification
    @if(session('success'))
        Notiflix.Notify.success('✅ {{ session("success") }}');
    @endif

    @if(session('password_success'))
        Notiflix.Notify.success('🔐 {{ session("password_success") }}');
    @endif

    function resetForm() {
        document.querySelector('#edit form').reset();
    }

    function clearPasswordForm() {
        document.querySelector('#password form').reset();
    }

    // Auto switch to edit tab if there are validation errors for profile
    @if($errors->has('name') || $errors->has('email'))
        document.addEventListener('DOMContentLoaded', function() {
            var editTab = new bootstrap.Tab(document.getElementById('edit-tab'));
            editTab.show();
        });
    @endif

    // Auto switch to password tab if there are validation errors for password
    @if($errors->has('current_password') || $errors->has('password'))
        document.addEventListener('DOMContentLoaded', function() {
            var passwordTab = new bootstrap.Tab(document.getElementById('password-tab'));
            passwordTab.show();
        });
    @endif
</script>
@endpush