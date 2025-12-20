@extends('layouts.auth')

@section('title', 'Daftar - FASHION SAAZZ')
@section('subtitle', 'Buat akun baru')

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
            <label for="name" class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                value="{{ old('name') }}" required autofocus>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="email" class="form-label">Alamat Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                value="{{ old('email') }}" required>
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
            <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-block w-100">
                <i class="fas fa-user-plus me-2"></i>Buat Akun
            </button>
        </div>

        <div class="text-center">
            <p class="mb-0">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-decoration-none">Masuk di sini</a>
            </p>
        </div>
    </form>
@endsection