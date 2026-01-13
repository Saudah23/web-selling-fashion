<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>@yield('title', 'FASHION SAAZZ Dashboard')</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
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

    <!-- JS Grid CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jsgrid/1.5.3/jsgrid.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jsgrid/1.5.3/jsgrid-theme.min.css" />

    <!-- Notiflix CSS -->
    <link rel="stylesheet" href="{{ asset('notiflix-Notiflix-67ba12d/dist/notiflix-3.2.8.min.css') }}" />

    <style>
        .role-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
        }

        .role-owner {
            background-color: #dc2626;
            color: white;
        }

        .role-admin {
            background-color: #2563eb;
            color: white;
        }

        .role-customer {
            background-color: #16a34a;
            color: white;
        }

        /* JS Grid Global Styles */
        .jsgrid {
            position: relative;
            overflow: auto;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
        }

        .jsgrid-table {
            width: 100%;
            table-layout: auto;
            margin-bottom: 0;
        }

        .jsgrid-header-table,
        .jsgrid-filter-table,
        .jsgrid-table {
            min-width: 100%;
        }

        .jsgrid-header-cell {
            background-color: #f8f9fa;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }

        .jsgrid-cell {
            border-bottom: 1px solid #f1f3f4;
        }

        .jsgrid-alt-row .jsgrid-cell {
            background-color: #f8f9fa;
        }

        /* Responsive JS Grid */
        @media (max-width: 992px) {

            .jsgrid-header-cell,
            .jsgrid-filter-cell,
            .jsgrid-cell {
                font-size: 13px;
                padding: 8px 6px;
            }
        }

        @media (max-width: 768px) {

            .jsgrid-header-cell,
            .jsgrid-filter-cell,
            .jsgrid-cell {
                font-size: 12px;
                padding: 6px 4px;
            }

            .slug-column {
                display: none !important;
            }

            .description-column {
                max-width: 120px;
            }

            .actions-column .btn {
                padding: 2px 6px;
                font-size: 11px;
            }
        }

        @media (max-width: 576px) {

            .parent-column,
            .description-column {
                display: none !important;
            }

            .actions-column .btn-group {
                flex-direction: column;
            }

            .actions-column .btn {
                margin-bottom: 2px;
                width: 100%;
            }

            .jsgrid {
                font-size: 11px;
            }

            .jsgrid-header-cell,
            .jsgrid-filter-cell,
            .jsgrid-cell {
                padding: 4px 2px;
            }
        }

        /* Badge improvements */
        .badge-sm {
            font-size: 0.65em;
            padding: 0.25em 0.5em;
        }

        /* Button group improvements */
        .btn-group-sm>.btn,
        .btn-sm {
            padding: 0.25rem 0.4rem;
            font-size: 0.75rem;
            border-radius: 0.2rem;
        }

        /* Pagination styling */
        .jsgrid-pager {
            text-align: center;
            margin-top: 1rem;
            padding: 1rem 0;
            border-top: 1px solid #dee2e6;
            background-color: #f8f9fa;
        }

        .jsgrid-pager-nav-button {
            margin: 0 2px;
            padding: 6px 12px;
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            text-decoration: none;
            color: #495057;
            transition: all 0.2s;
        }

        .jsgrid-pager-nav-button:hover {
            background: #e9ecef;
            color: #212529;
            text-decoration: none;
        }

        .jsgrid-pager-current-page {
            font-weight: bold;
            color: #007bff;
            background: #007bff;
            color: white;
        }

        .jsgrid-pager-page {
            margin: 0 1px;
        }

        /* Notiflix Button Text Visibility Fix */
        .notiflix-confirm-button {
            color: inherit !important;
            text-decoration: none !important;
            opacity: 1 !important;
        }

        .notiflix-confirm-button:hover {
            opacity: 0.8 !important;
        }

        /* Fix button text colors specifically */
        div[id^="NotiflixConfirmWrap"] .notiflix-confirm-button {
            font-weight: 500 !important;
            letter-spacing: 0.025em !important;
        }

        /* OK Button - Red */
        div[id^="NotiflixConfirmWrap"] .notiflix-confirm-button:first-of-type {
            color: #ffffff !important;
            background-color: #dc2626 !important;
            border-color: #dc2626 !important;
        }

        /* Cancel Button - Gray */
        div[id^="NotiflixConfirmWrap"] .notiflix-confirm-button:last-of-type {
            color: #374151 !important;
            background-color: #f3f4f6 !important;
            border-color: #e5e7eb !important;
        }

        /* Hover effects */
        div[id^="NotiflixConfirmWrap"] .notiflix-confirm-button:first-of-type:hover {
            background-color: #b91c1c !important;
            border-color: #b91c1c !important;
        }

        div[id^="NotiflixConfirmWrap"] .notiflix-confirm-button:last-of-type:hover {
            background-color: #e5e7eb !important;
            color: #1f2937 !important;
        }
    </style>

    @stack('styles')
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar" data-background-color="dark">
            <div class="sidebar-logo">
                <div class="logo-header" data-background-color="dark">
                    <a href="{{ url('/') }}" class="logo">
                        <img src="{{ asset('LogoFahionSaazz.jpg') }}" alt="Fashion Saazz" style="height: 35px; border-radius: 5px;">
                    </a>
                    <div class="nav-toggle">
                        <button class="btn btn-toggle toggle-sidebar">
                            <i class="gg-menu-right"></i>
                        </button>
                        <button class="btn btn-toggle sidenav-toggler">
                            <i class="gg-menu-left"></i>
                        </button>
                    </div>
                    <button class="topbar-toggler more">
                        <i class="gg-more-vertical-alt"></i>
                    </button>
                </div>
            </div>
            <div class="sidebar-wrapper scrollbar scrollbar-inner">
                <div class="sidebar-content">
                    <ul class="nav nav-secondary">
                        @auth
                            @if(auth()->user()->role === 'admin')
                                @include('components.admin-sidebar')
                            @elseif(auth()->user()->role === 'owner')
                                @include('components.owner-sidebar')
                            @elseif(auth()->user()->role === 'customer')
                                @include('components.customer-sidebar')
                            @endif
                        @else
                            @yield('sidebar')
                        @endauth
                    </ul>
                </div>
            </div>
        </div>

        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <div class="logo-header" data-background-color="dark">
                        <a href="{{ url('/') }}" class="logo">
                            <span style="font-size: 1.25rem; font-weight: 700; color: #fff;">FASHION SAAZZ</span>
                        </a>
                        <button class="navbar-toggler sidenav-toggler ml-auto" type="button" data-toggle="collapse"
                            data-target="collapse" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon">
                                <i class="icon-menu"></i>
                            </span>
                        </button>
                        <button class="topbar-toggler more">
                            <i class="icon-options-vertical"></i>
                        </button>
                    </div>
                </div>

                <!-- Navbar Header -->
                <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
                    <div class="container-fluid">
                        <!-- Homepage Link -->
                        <div class="navbar-brand d-flex align-items-center">
                            <a href="{{ route('home') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-home me-2"></i>
                                Ke Homepage
                            </a>
                        </div>

                        <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                            <li class="nav-item topbar-user dropdown hidden-caret">
                                <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#"
                                    aria-expanded="false">
                                    <div class="avatar-sm">
                                        <img src="{{ asset('kaiadmin-lite-1.2.0/assets/img/profile.jpg') }}" alt="..."
                                            class="avatar-img rounded-circle" />
                                    </div>
                                    <span class="profile-username">
                                        <span class="fw-bold">{{ auth()->user()->name }}</span>
                                    </span>
                                </a>
                                <ul class="dropdown-menu dropdown-user animated fadeIn">
                                    <div class="dropdown-user-scroll scrollbar-outer">
                                        <li>
                                            <div class="user-box">
                                                <div class="avatar-lg">
                                                    <img src="{{ asset('kaiadmin-lite-1.2.0/assets/img/profile.jpg') }}"
                                                        alt="image profile" class="avatar-img rounded" />
                                                </div>
                                                <div class="u-text">
                                                    <h4>{{ auth()->user()->name }}</h4>
                                                    <p class="text-muted">{{ auth()->user()->email }}</p>
                                                    <span class="role-badge role-{{ auth()->user()->role }}">
                                                        {{ ucfirst(auth()->user()->role) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="{{ route('profile.show') }}">Profil Saya</a>
                                            @if(auth()->user()->role === 'customer')
                                                <a class="dropdown-item" href="{{ route('addresses.index') }}">Alamat Saya</a>
                                            @elseif(auth()->user()->role === 'admin')
                                                <a class="dropdown-item" href="{{ route('admin.settings.index') }}">Pengaturan Sistem</a>
                                            @elseif(auth()->user()->role === 'owner')
                                                <a class="dropdown-item" href="{{ route('owner.settings.index') }}">Pengaturan Sistem</a>
                                            @endif
                                            <div class="dropdown-divider"></div>
                                            <form method="POST" action="{{ route('logout') }}" id="logout-form">
                                                @csrf
                                                <button type="button" class="dropdown-item"
                                                    onclick="confirmLogout()">Keluar</button>
                                            </form>
                                        </li>
                                    </div>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>

            <div class="container">
                <div class="page-inner">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <!-- Core JS Files -->
    <script src="{{ asset('kaiadmin-lite-1.2.0/assets/js/core/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('kaiadmin-lite-1.2.0/assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('kaiadmin-lite-1.2.0/assets/js/core/bootstrap.min.js') }}"></script>

    <!-- jQuery Scrollbar -->
    <script src="{{ asset('kaiadmin-lite-1.2.0/assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>

    <!-- JS Grid -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsgrid/1.5.3/jsgrid.min.js"></script>

    <!-- Kaiadmin JS -->
    <script src="{{ asset('kaiadmin-lite-1.2.0/assets/js/kaiadmin.min.js') }}"></script>

    <!-- Notiflix JS -->
    <script src="{{ asset('notiflix-Notiflix-67ba12d/dist/notiflix-aio-3.2.8.min.js') }}"></script>

    <script>
        // Global Notiflix Configuration
        Notiflix.Notify.init({
            width: '320px',
            position: 'right-top',
            distance: '20px',
            opacity: 1,
            borderRadius: '8px',
            rtl: false,
            timeout: 4000,
            messageMaxLength: 120,
            backOverlay: false,
            plainText: true,
            showOnlyTheLastOne: false,
            clickToClose: true,
            pauseOnHover: true,
            ID: 'NotiflixNotify',
            className: 'notiflix-notify',
            zindex: 4001,
            fontFamily: 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
            fontSize: '14px',
            cssAnimation: true,
            cssAnimationDuration: 300,
            cssAnimationStyle: 'fade',
            closeButton: false,
            useIcon: true,
            success: {
                background: '#10b981',
                textColor: '#ffffff',
                childClassName: 'notiflix-notify-success',
                notiflixIconColor: 'rgba(255,255,255,0.3)',
                backOverlayColor: 'rgba(16,185,129,0.2)',
            },
            failure: {
                background: '#ef4444',
                textColor: '#ffffff',
                childClassName: 'notiflix-notify-failure',
                notiflixIconColor: 'rgba(255,255,255,0.3)',
                backOverlayColor: 'rgba(239,68,68,0.2)',
            },
            warning: {
                background: '#f59e0b',
                textColor: '#ffffff',
                childClassName: 'notiflix-notify-warning',
                notiflixIconColor: 'rgba(255,255,255,0.3)',
                backOverlayColor: 'rgba(245,158,11,0.2)',
            },
            info: {
                background: '#3b82f6',
                textColor: '#ffffff',
                childClassName: 'notiflix-notify-info',
                notiflixIconColor: 'rgba(255,255,255,0.3)',
                backOverlayColor: 'rgba(59,130,246,0.2)',
            },
        });

        // Global Confirm Dialog Configuration
        Notiflix.Confirm.init({
            width: '400px',
            backgroundColor: '#ffffff',
            titleColor: '#1f2937',
            titleFontSize: '18px',
            titleMaxLength: 34,
            messageColor: '#4b5563',
            messageFontSize: '15px',
            messageMaxLength: 400,
            buttonsFontSize: '16px',
            buttonsMaxLength: 34,
            okButtonColor: '#ffffff !important',
            okButtonBackground: '#dc2626',
            cancelButtonColor: '#374151 !important',
            cancelButtonBackground: '#f3f4f6',
            backOverlay: true,
            backOverlayColor: 'rgba(0,0,0,0.6)',
            rtl: false,
            fontFamily: 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
            cssAnimation: true,
            cssAnimationStyle: 'zoom',
            cssAnimationDuration: 300,
            plainText: true,
            borderRadius: '12px',
        });

        // Global Loading Configuration
        Notiflix.Loading.init({
            className: 'notiflix-loading',
            zindex: 4000,
            backgroundColor: 'rgba(0,0,0,0.8)',
            rtl: false,
            fontFamily: 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
            cssAnimation: true,
            cssAnimationDuration: 400,
            clickToClose: false,
            customSvgUrl: null,
            customSvgCode: null,
            svgSize: '80px',
            svgColor: '#32c682',
            messageID: 'NotiflixLoadingMessage',
            messageFontSize: '15px',
            messageMaxLength: 34,
            messageColor: '#dbeafe',
        });

        function confirmLogout() {
            Notiflix.Confirm.show(
                'Konfirmasi Keluar',
                'Apakah Anda yakin ingin keluar dari FASHION SAAZZ?',
                'Ya, Keluar',
                'Batal',
                function okCb() {
                    // Show loading
                    Notiflix.Loading.circle('Sedang keluar...');

                    // Submit logout form
                    document.getElementById('logout-form').submit();
                },
                function cancelCb() {
                    // User cancelled
                },
                {
                    width: '400px',
                    borderRadius: '12px',
                    titleColor: '#ff5549',
                    okButtonBackground: '#ff5549',
                }
            );
        }

        // Show welcome notification if user just logged in
        @if(session('login_success'))
            document.addEventListener('DOMContentLoaded', function () {
                Notiflix.Notify.success('🎉 Selamat datang kembali di FASHION SAAZZ!');
            });
        @endif

        // Show logout success notification
        @if(session('logout_success'))
            document.addEventListener('DOMContentLoaded', function () {
                Notiflix.Notify.success('👋 Berhasil keluar! Sampai jumpa lagi!');
            });
        @endif
    </script>

    @stack('scripts')
</body>

</html>