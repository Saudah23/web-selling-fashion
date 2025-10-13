@extends('layouts.auth')

@section('title', 'Register - Fashion Marketplace')
@section('subtitle', 'Create your account')

@section('content')
<form method="POST" action="{{ route('register') }}">
    @csrf

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-group">
        <label for="name" class="form-label">Full Name</label>
        <input type="text"
               class="form-control @error('name') is-invalid @enderror"
               id="name"
               name="name"
               value="{{ old('name') }}"
               required
               autofocus>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="email" class="form-label">Email Address</label>
        <input type="email"
               class="form-control @error('email') is-invalid @enderror"
               id="email"
               name="email"
               value="{{ old('email') }}"
               required>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="role" class="form-label">Account Type</label>
        <select class="form-control @error('role') is-invalid @enderror" id="role" name="role" required>
            <option value="">Select Account Type</option>
            <option value="customer" {{ old('role') == 'customer' ? 'selected' : '' }}>Customer</option>
            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="owner" {{ old('role') == 'owner' ? 'selected' : '' }}>Owner</option>
        </select>
        @error('role')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="form-text text-muted">
            <i class="fas fa-info-circle"></i> Customer: Browse and buy products | Admin: Manage products | Owner: Full control
        </small>
    </div>

    <div class="form-group">
        <label for="password" class="form-label">Password</label>
        <input type="password"
               class="form-control @error('password') is-invalid @enderror"
               id="password"
               name="password"
               required>
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="password_confirmation" class="form-label">Confirm Password</label>
        <input type="password"
               class="form-control"
               id="password_confirmation"
               name="password_confirmation"
               required>
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary btn-block w-100">
            <i class="fas fa-user-plus me-2"></i>Create Account
        </button>
    </div>

    <div class="text-center">
        <p class="mb-0">
            Already have an account?
            <a href="{{ route('login') }}" class="text-decoration-none">Sign in here</a>
        </p>
    </div>
</form>
@endsection