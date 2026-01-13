<!DOCTYPE html>
<html lang="id">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>@yield('title', 'FASHION SAAZZ')</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="{{ asset('kaiadmin-lite-1.2.0/assets/img/kaiadmin/favicon.ico') }}" type="image/x-icon" />

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('kaiadmin-lite-1.2.0/assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />

    <!-- Notiflix CSS -->
    <link rel="stylesheet" href="{{ asset('notiflix-Notiflix-67ba12d/dist/notiflix-3.2.8.min.css') }}" />

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-container {
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }

        .auth-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        .auth-header {
            text-align: center;
            padding: 40px 32px 24px;
        }

        .brand-logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 64px;
            height: 64px;
            background: #1f2937;
            border-radius: 16px;
            margin-bottom: 20px;
        }

        .brand-logo i {
            font-size: 28px;
            color: #ffffff;
        }

        .brand-name {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            letter-spacing: -0.5px;
        }

        .brand-name span {
            color: #ef4444;
        }

        .auth-subtitle {
            color: #64748b;
            font-size: 14px;
            margin-top: 8px;
        }

        .auth-body {
            padding: 0 32px 32px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 6px;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.2s ease;
            background: #f9fafb;
        }

        .form-control:focus {
            outline: none;
            border-color: #1f2937;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(31, 41, 55, 0.1);
        }

        .form-control.is-invalid {
            border-color: #ef4444;
        }

        .invalid-feedback {
            font-size: 12px;
            color: #ef4444;
            margin-top: 4px;
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-check-input {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            border: 1px solid #d1d5db;
        }

        .form-check-label {
            font-size: 13px;
            color: #4b5563;
        }

        .btn-primary {
            width: 100%;
            padding: 12px 24px;
            background: #1f2937;
            border: none;
            border-radius: 10px;
            color: #ffffff;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            background: #374151;
            transform: translateY(-1px);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .alert-danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .alert ul {
            margin: 0;
            padding-left: 16px;
        }

        .auth-footer {
            text-align: center;
            margin-top: 20px;
        }

        .auth-footer p {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 8px;
        }

        .auth-footer a {
            color: #1f2937;
            font-weight: 500;
            text-decoration: none;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 24px 0;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }

        .divider span {
            padding: 0 12px;
            font-size: 12px;
            color: #9ca3af;
        }

        .back-home {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 24px;
            font-size: 13px;
            color: #6b7280;
            text-decoration: none;
        }

        .back-home:hover {
            color: #1f2937;
        }
    </style>
</head>

<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="brand-logo" style="width: auto; height: auto; background: transparent; border-radius: 0;">
                    <img src="{{ asset('LogoFahionSaazz.jpg') }}" alt="Fashion Saazz" style="height: 60px; border-radius: 10px;">
                </div>
                <h1 class="brand-name">FASHION <span>SAAZZ</span></h1>
                <p class="auth-subtitle">@yield('subtitle', 'Selamat Datang')</p>
            </div>
            <div class="auth-body">
                @yield('content')
            </div>
        </div>

        <div class="text-center">
            <a href="{{ route('home') }}" class="back-home">
                <i class="fas fa-arrow-left"></i>
                Kembali ke Beranda
            </a>
        </div>
    </div>

    <!-- Core JS Files -->
    <script src="{{ asset('kaiadmin-lite-1.2.0/assets/js/core/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('kaiadmin-lite-1.2.0/assets/js/core/bootstrap.min.js') }}"></script>

    <!-- Notiflix JS -->
    <script src="{{ asset('notiflix-Notiflix-67ba12d/dist/notiflix-aio-3.2.8.min.js') }}"></script>

    @stack('scripts')
</body>

</html>