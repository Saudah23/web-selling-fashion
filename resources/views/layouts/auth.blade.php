<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>@yield('title', 'Marketplace Fashion')</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="{{ asset('kaiadmin-lite-1.2.0/assets/img/kaiadmin/favicon.ico') }}" type="image/x-icon" />

    <!-- Fonts and icons -->
    <script src="{{ asset('kaiadmin-lite-1.2.0/assets/js/plugin/webfont/webfont.min.js') }}"></script>
    <script>
        WebFont.load({
            google: { families: ["Public Sans:300,400,500,600,700"] },
            custom: {
                families: [
                    "Font Awesome 5 Solid",
                    "Font Awesome 5 Regular",
                    "Font Awesome 5 Brands",
                    "simple-line-icons",
                ],
                urls: ["{{ asset('kaiadmin-lite-1.2.0/assets/css/fonts.min.css') }}"],
            },
            active: function () {
                sessionStorage.fonts = true;
            },
        });
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('kaiadmin-lite-1.2.0/assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('kaiadmin-lite-1.2.0/assets/css/plugins.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('kaiadmin-lite-1.2.0/assets/css/kaiadmin.min.css') }}" />

    <!-- Notiflix CSS -->
    <link rel="stylesheet" href="{{ asset('notiflix-Notiflix-67ba12d/dist/notiflix-3.2.8.min.css') }}" />

    <style>
        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
        }
        .auth-card {
            width: 100%;
            max-width: 400px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .auth-header {
            background: #1f2937;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .auth-body {
            padding: 30px;
        }
        .fashion-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        .btn-primary {
            background-color: #1f2937;
            border-color: #1f2937;
        }
        .btn-primary:hover {
            background-color: #374151;
            border-color: #374151;
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-header">
                <i class="fas fa-tshirt fashion-icon"></i>
                <h2>Fashion Marketplace</h2>
                <p class="mb-0">@yield('subtitle', 'Welcome Back')</p>
            </div>
            <div class="auth-body">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Core JS Files -->
    <script src="{{ asset('kaiadmin-lite-1.2.0/assets/js/core/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('kaiadmin-lite-1.2.0/assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('kaiadmin-lite-1.2.0/assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('kaiadmin-lite-1.2.0/assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>
    <script src="{{ asset('kaiadmin-lite-1.2.0/assets/js/kaiadmin.min.js') }}"></script>

    <!-- Notiflix JS -->
    <script src="{{ asset('notiflix-Notiflix-67ba12d/dist/notiflix-aio-3.2.8.min.js') }}"></script>

    @stack('scripts')
</body>
</html>