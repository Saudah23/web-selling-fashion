@extends('layouts.auth')

@section('title', 'Masuk - FASHION SAAZZ')
@section('subtitle', 'Masuk ke akun Anda')

@section('content')
    @if(session('logout_success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Notiflix.Notify.success('👋 Berhasil keluar! Sampai jumpa lagi di FASHION SAAZZ!');
            });
        </script>
    @endif
    <form method="POST" action="{{ route('login') }}">
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
            <label for="email" class="form-label">Alamat Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                value="{{ old('email') }}" required autofocus>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Kata Sandi</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                name="password" required>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label" for="remember">
                    Ingat saya
                </label>
            </div>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-block w-100">
                <i class="fas fa-sign-in-alt me-2"></i>Masuk
            </button>
        </div>

        <div class="text-center">
            <p class="mb-2">
                <a href="{{ route('password.request') }}" class="text-decoration-none">
                    Lupa kata sandi?
                </a>
            </p>
            <p class="mb-0">
                Belum punya akun?
                <a href="{{ route('register') }}" class="text-decoration-none">Daftar di sini</a>
            </p>
        </div>
    </form>
@endsection