@extends('layouts.auth')

@section('title', 'Reset Password - Fashion Marketplace')
@section('subtitle', 'Forgot your password?')

@section('content')
<div class="text-center mb-4">
    <p class="text-muted">Enter your email address and we'll send you a link to reset your password.</p>
</div>

<form method="POST" action="{{ route('password.email') }}">
    @csrf

    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

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
        <label for="email" class="form-label">Email Address</label>
        <input type="email"
               class="form-control @error('email') is-invalid @enderror"
               id="email"
               name="email"
               value="{{ old('email') }}"
               required
               autofocus>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary btn-block w-100">
            <i class="fas fa-paper-plane me-2"></i>Send Password Reset Link
        </button>
    </div>

    <div class="text-center">
        <p class="mb-0">
            Remember your password?
            <a href="{{ route('login') }}" class="text-decoration-none">Back to login</a>
        </p>
    </div>
</form>
@endsection