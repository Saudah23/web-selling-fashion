<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="author" content="Marketplace Laravel">
  <link rel="shortcut icon" href="{{ asset('furni-1.0.0/favicon.png') }}">

  <meta name="description" content="@yield('description', 'Modern furniture marketplace')" />
  <meta name="keywords" content="furniture, interior, marketplace, shopping" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />

  <!-- Bootstrap CSS -->
  <link href="{{ asset('furni-1.0.0/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link href="{{ asset('furni-1.0.0/css/tiny-slider.css') }}" rel="stylesheet">
  <link href="{{ asset('furni-1.0.0/css/style.css') }}" rel="stylesheet">
  <!-- Notiflix CSS -->
  <link href="{{ asset('notiflix-Notiflix-67ba12d/dist/notiflix-3.2.8.min.css') }}" rel="stylesheet">

  @yield('styles')

  <!-- Custom Navbar Styles -->
  <style>
    /* Navbar Background and Structure */
    .custom-navbar {
      background-color: white !important;
      border-bottom: 1px solid #f1f5f9;
      padding: 12px 0;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1050;
      transition: all 0.3s ease;
    }

    /* Brand Styling */
    .custom-navbar .navbar-brand {
      color: #2c3e50 !important;
      font-weight: 700;
      font-size: 1.6rem;
      letter-spacing: -0.5px;
    }

    .custom-navbar .navbar-brand span {
      color: #ff6b6b !important;
    }

    /* Navigation Links */
    .custom-navbar .nav-link {
      color: #64748b !important;
      font-weight: 400;
      font-size: 12px;
      padding: 0 !important;
      margin: 0 4px;
      background: none !important;
      border: none !important;
      border-radius: 0 !important;
      transition: all 0.2s ease;
      text-decoration: none !important;
      border-bottom: none !important;
      box-shadow: none !important;
    }

    .custom-navbar .nav-link:hover {
      color: #ff6b6b !important;
      background: none !important;
      text-decoration: none !important;
      border-bottom: none !important;
      box-shadow: none !important;
    }

    /* Active State */
    .custom-navbar .nav-item.active .nav-link,
    .custom-navbar li.active .nav-link {
      color: #ff6b6b !important;
      background: none !important;
      font-weight: 500;
      text-decoration: none !important;
      border-bottom: none !important;
      box-shadow: none !important;
    }

    /* Force override original template styles */
    .custom-navbar .nav-link,
    .custom-navbar .navbar-nav .nav-link,
    .custom-navbar .custom-navbar-nav .nav-link {
      color: #64748b !important;
      background: none !important;
      background-color: transparent !important;
      background-image: none !important;
      border: none !important;
      border-bottom: none !important;
      text-shadow: none !important;
      box-shadow: none !important;
      outline: none !important;
      text-decoration: none !important;
    }

    /* Force hover states */
    .custom-navbar .nav-link:hover,
    .custom-navbar .navbar-nav .nav-link:hover,
    .custom-navbar .custom-navbar-nav .nav-link:hover {
      color: #ff6b6b !important;
      background: none !important;
      background-color: transparent !important;
      border-bottom: none !important;
      text-decoration: none !important;
      box-shadow: none !important;
    }

    /* Force active states */
    .custom-navbar .nav-item.active .nav-link,
    .custom-navbar li.active .nav-link,
    .custom-navbar .navbar-nav .active .nav-link,
    .custom-navbar .custom-navbar-nav .active .nav-link {
      color: #ff6b6b !important;
      background: none !important;
      background-color: transparent !important;
      border-bottom: none !important;
      text-decoration: none !important;
      box-shadow: none !important;
    }

    /* Ultra-strong overrides for navbar text visibility */
    nav.custom-navbar a,
    nav.custom-navbar a.nav-link,
    .custom-navbar a,
    .custom-navbar a.nav-link,
    .navbar.custom-navbar a,
    .navbar.custom-navbar a.nav-link {
      color: #64748b !important;
      text-decoration: none !important;
      border-bottom: none !important;
      box-shadow: none !important;
    }

    nav.custom-navbar a:hover,
    nav.custom-navbar a.nav-link:hover,
    .custom-navbar a:hover,
    .custom-navbar a.nav-link:hover,
    .navbar.custom-navbar a:hover,
    .navbar.custom-navbar a.nav-link:hover {
      color: #ff6b6b !important;
      text-decoration: none !important;
      border-bottom: none !important;
      box-shadow: none !important;
    }

    /* Focus states */
    .custom-navbar .navbar-nav .nav-link:focus {
      color: #ff6b6b !important;
      outline: none !important;
      box-shadow: none !important;
      text-decoration: none !important;
      border-bottom: none !important;
    }

    /* Visited states */
    .custom-navbar a:visited,
    .custom-navbar .nav-link:visited {
      color: #64748b !important;
      text-decoration: none !important;
      border-bottom: none !important;
    }

    /* Dropdown Styling */
    .custom-navbar .dropdown-menu {
      border: 1px solid #e0e0e0;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
      background: white;
      border-radius: 6px;
      padding: 4px 0;
      margin-top: 4px;
      z-index: 1100 !important;
      position: absolute !important;
      min-width: 120px;
    }

    .custom-navbar .dropdown-item {
      color: #2c3e50 !important;
      font-weight: 400;
      font-size: 0.8rem;
      padding: 4px 12px;
      transition: all 0.3s ease;
      border: none;
      background: none;
      white-space: nowrap;
    }

    /* Additional dropdown item color overrides */
    .custom-navbar .dropdown-menu .dropdown-item,
    .custom-navbar .dropdown-menu a.dropdown-item {
      color: #2c3e50 !important;
      text-decoration: none !important;
    }

    .custom-navbar .dropdown-item:hover,
    .custom-navbar .dropdown-item:focus {
      background-color: rgba(255, 107, 107, 0.08) !important;
      color: #ff6b6b !important;
    }

    .custom-navbar .dropdown-item.active {
      background-color: rgba(255, 107, 107, 0.1) !important;
      color: #ff6b6b !important;
      font-weight: 600;
    }

    /* Dropdown Toggle Arrow */
    .custom-navbar .dropdown-toggle::after {
      border: none;
      font-family: "Font Awesome 5 Free";
      font-weight: 900;
      content: "\f078";
      font-size: 10px;
      margin-left: 8px;
      vertical-align: middle;
      transition: transform 0.3s ease;
    }

    .custom-navbar .dropdown-toggle[aria-expanded="true"]::after {
      transform: rotate(180deg);
    }

    /* Mobile Toggle */
    .custom-navbar .navbar-toggler {
      border: 1px solid #d0d0d0;
      padding: 6px 10px;
      border-radius: 6px;
    }

    .custom-navbar .navbar-toggler:focus {
      box-shadow: 0 0 0 0.2rem rgba(255, 107, 107, 0.25);
      border-color: #ff6b6b;
    }

    .custom-navbar .navbar-toggler-icon {
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%2852, 73, 94, 0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
      width: 20px;
      height: 20px;
    }

    /* User and Cart Icons */
    .custom-navbar-cta .nav-link {
      padding: 8px 12px;
      border-radius: 6px;
      transition: all 0.3s ease;
    }

    .custom-navbar-cta .nav-link:hover {
      background: rgba(255, 107, 107, 0.1) !important;
    }

    .custom-navbar-cta .nav-link img {
      opacity: 0.8;
      transition: opacity 0.3s ease;
      filter: brightness(0.6);
    }

    .custom-navbar-cta .nav-link:hover img {
      opacity: 1;
      filter: brightness(0.4) sepia(1) hue-rotate(340deg) saturate(2);
    }

    /* Cart Icon with Counter */
    .cart-icon-wrapper {
      position: relative;
      display: inline-block;
    }

    .cart-counter {
      position: absolute;
      top: -8px;
      right: -8px;
      background: #ff6b6b;
      color: white;
      border-radius: 50%;
      padding: 2px 6px;
      font-size: 11px;
      font-weight: 600;
      min-width: 18px;
      height: 18px;
      display: flex;
      align-items: center;
      justify-content: center;
      line-height: 1;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    /* Back to Top Button */
    .back-to-top {
      position: fixed;
      bottom: 30px;
      right: 30px;
      background: #ff6b6b;
      color: white;
      width: 50px;
      height: 50px;
      border-radius: 50%;
      display: none;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      transition: all 0.3s ease;
      z-index: 1000;
      box-shadow: 0 4px 12px rgba(255, 107, 107, 0.3);
      line-height: 1;
    }

    .back-to-top:hover {
      background: #e55555;
      color: white;
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(255, 107, 107, 0.4);
      text-decoration: none;
    }

    .back-to-top i {
      font-size: 16px;
      line-height: 1;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    /* Body padding to compensate for fixed navbar */
    body {
      padding-top: 70px;
    }

    /* Mobile Navigation Responsive Styles */
    @media (max-width: 991px) {

      /* Mobile dropdown container */
      .custom-navbar .dropdown-menu {
        position: static !important;
        float: none;
        width: auto;
        margin-top: 0;
        background-color: #ffffff;
        border: 1px solid #e9ecef;
        box-shadow: none;
        border-radius: 4px;
        padding: 8px 0;
      }

      /* Mobile dropdown items - Clean and explicit */
      .custom-navbar .dropdown-item,
      .custom-navbar .dropdown-menu .dropdown-item,
      .custom-navbar .dropdown-menu a.dropdown-item,
      nav.custom-navbar .dropdown-item,
      nav.custom-navbar .dropdown-menu .dropdown-item,
      nav.custom-navbar .dropdown-menu a.dropdown-item {
        padding: 12px 20px !important;
        font-size: 14px !important;
        color: #2c3e50 !important;
        background-color: transparent !important;
        border: none !important;
        text-decoration: none !important;
        display: block !important;
        width: 100% !important;
        clear: both !important;
        font-weight: 400 !important;
        line-height: 1.42857143 !important;
        white-space: nowrap !important;
      }

      /* Mobile dropdown hover and active states */
      .custom-navbar .dropdown-item:hover,
      .custom-navbar .dropdown-item:focus,
      .custom-navbar .dropdown-menu .dropdown-item:hover,
      .custom-navbar .dropdown-menu .dropdown-item:focus,
      .custom-navbar .dropdown-menu a.dropdown-item:hover,
      .custom-navbar .dropdown-menu a.dropdown-item:focus,
      nav.custom-navbar .dropdown-item:hover,
      nav.custom-navbar .dropdown-item:focus,
      nav.custom-navbar .dropdown-menu .dropdown-item:hover,
      nav.custom-navbar .dropdown-menu .dropdown-item:focus,
      nav.custom-navbar .dropdown-menu a.dropdown-item:hover,
      nav.custom-navbar .dropdown-menu a.dropdown-item:focus {
        color: #ff6b6b !important;
        background-color: rgba(255, 107, 107, 0.1) !important;
        text-decoration: none !important;
      }

      .custom-navbar .dropdown-item.active,
      .custom-navbar .dropdown-menu .dropdown-item.active,
      .custom-navbar .dropdown-menu a.dropdown-item.active,
      nav.custom-navbar .dropdown-item.active,
      nav.custom-navbar .dropdown-menu .dropdown-item.active,
      nav.custom-navbar .dropdown-menu a.dropdown-item.active {
        color: #ff6b6b !important;
        background-color: rgba(255, 107, 107, 0.15) !important;
        font-weight: 500 !important;
      }

      /* Mobile dropdown toggle arrow */
      .custom-navbar .dropdown-toggle::after {
        float: right;
        margin-top: 8px;
      }

      body {
        padding-top: 70px;
      }
    }

    @media (max-width: 768px) {
      body {
        padding-top: 65px;
      }

      /* Additional fallback for dropdown visibility */
      .dropdown-menu .dropdown-item {
        color: #2c3e50 !important;
        background: transparent !important;
      }

      .dropdown-menu .dropdown-item:hover {
        color: #ff6b6b !important;
        background: rgba(255, 107, 107, 0.1) !important;
      }
    }
  </style>

  <title>@yield('title', 'FASHION SAAZZ - Toko Fashion Online Terpercaya')</title>
</head>

<body>

  <!-- Start Header/Navigation -->
  <nav class="custom-navbar navbar navbar navbar-expand-md navbar-light bg-white border-bottom"
    arial-label="Furni navigation bar">
    <div class="container">
      <a class="navbar-brand"
        href="{{ route('home') }}">{{ $systemSettings['app_name'] ?? 'Marketplace' }}<span>.</span></a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsFurni"
        aria-controls="navbarsFurni" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarsFurni">
        <ul class="custom-navbar-nav navbar-nav ms-auto mb-2 mb-md-0">
          <li class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('home') }}">Beranda</a>
          </li>
          <li class="nav-item {{ request()->routeIs('shop') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('shop') }}">Katalog</a>
          </li>
          @if(isset($globalCategories))
            @foreach($globalCategories as $parentCategory)
              <li
                class="nav-item dropdown {{ collect($parentCategory->children)->pluck('id')->contains(request('category')) ? 'active' : '' }}">
                @if($parentCategory->children->count() > 0)
                  <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    {{ $parentCategory->name }}
                  </a>
                  <ul class="dropdown-menu">
                    @foreach($parentCategory->children as $subCategory)
                      <li>
                        <a class="dropdown-item {{ request('category') == $subCategory->id ? 'active' : '' }}"
                          href="{{ route('shop', ['category' => $subCategory->id]) }}">
                          {{ $subCategory->name }}
                        </a>
                      </li>
                    @endforeach
                  </ul>
                @else
                  <a class="nav-link" href="{{ route('shop', ['category' => $parentCategory->id]) }}">
                    {{ $parentCategory->name }}
                  </a>
                @endif
              </li>
            @endforeach
          @endif
          <li><a class="nav-link"
              href="{{ request()->routeIs('home') ? '#contact' : route('home') . '#contact' }}">Hubungi Kami</a></li>
        </ul>

        <ul class="custom-navbar-cta navbar-nav mb-2 mb-md-0 ms-5">
          @auth
                    <!-- User menu for authenticated users -->
                    <li class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="{{ asset('furni-1.0.0/images/user.svg') }}">
                      </a>
                      <ul class="dropdown-menu">
                        <li>
                          <a class="dropdown-item" href="{{ match (auth()->user()->role) {
              'owner' => route('owner.dashboard'),
              'admin' => route('admin.dashboard'),
              'customer' => route('customer.dashboard'),
              default => route('customer.dashboard')
            } }}">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                          </a>
                        </li>
                        @if(auth()->user()->role === 'customer')
                          <li>
                            <a class="dropdown-item" href="{{ route('orders.index') }}">
                              <i class="fas fa-shopping-bag me-2"></i>Pesanan Saya
                            </a>
                          </li>
                          <li>
                            <a class="dropdown-item" href="{{ route('shop') }}?wishlist=1">
                              <i class="fas fa-heart me-2"></i>Wishlist
                            </a>
                          </li>
                          <li>
                            <a class="dropdown-item" href="{{ route('addresses.index') }}">
                              <i class="fas fa-map-marker-alt me-2"></i>Alamat Saya
                            </a>
                          </li>
                        @endif
                        <li>
                          <hr class="dropdown-divider">
                        </li>
                        <li>
                          <a class="dropdown-item" href="{{ route('profile.show') }}">
                            <i class="fas fa-user me-2"></i>Profil
                          </a>
                        </li>
                        <li>
                          <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">
                              <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </button>
                          </form>
                        </li>
                      </ul>
                    </li>
          @else
            <!-- Login/Register for guests -->
            <li>
              <a class="nav-link" href="{{ route('login') }}">
                <img src="{{ asset('furni-1.0.0/images/user.svg') }}">
              </a>
            </li>
          @endauth
          <li>
            <a class="nav-link cart-link" href="{{ route('cart.index') }}">
              <div class="cart-icon-wrapper">
                <img src="{{ asset('furni-1.0.0/images/cart.svg') }}" alt="Cart">
                @auth
                  <span class="cart-counter" id="cartCounter" style="display: none;">0</span>
                @endauth
              </div>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <!-- End Header/Navigation -->

  @yield('content')

  <!-- Start Footer Section -->
  <footer class="footer-section">
    <div class="container relative">
      <div class="row g-5 mb-5">
        <div class="col-lg-4">
          <div class="mb-4 footer-logo-wrap">
            <a href="{{ route('home') }}" class="footer-logo">
              @if(isset($systemSettings) && $systemSettings['app_name'])
                {{ $systemSettings['app_name'] }}<span>.</span>
              @else
                Fashion Marketplace<span>.</span>
              @endif
            </a>
          </div>
          <p class="mb-4">
            @if(isset($systemSettings) && $systemSettings['app_description'])
              {{ $systemSettings['app_description'] }}
            @else
              Premium fashion marketplace for modern clothing
            @endif
          </p>

          @if(isset($systemSettings) && ($systemSettings['contact_phone'] || $systemSettings['contact_email'] || isset($shippingAddress)))
            <div class="mb-4">
              @if($systemSettings['contact_phone'])
                <p class="mb-2"><i class="fab fa-whatsapp me-2 text-primary"></i> {{ $systemSettings['contact_phone'] }}</p>
              @endif
              @if($systemSettings['contact_email'])
                <p class="mb-2"><i class="fas fa-envelope me-2 text-primary"></i> {{ $systemSettings['contact_email'] }}</p>
              @endif
              @if(isset($systemSettings['contact_instagram']))
                <p class="mb-2"><a href="https://instagram.com/{{ ltrim($systemSettings['contact_instagram'], '@') }}"
                    target="_blank" class="text-decoration-none"><i class="fab fa-instagram me-2 text-primary"></i>
                    {{ $systemSettings['contact_instagram'] }}</a></p>
              @endif
              @if(isset($systemSettings['contact_address']))
                <p class="mb-2"><i class="fas fa-map-marker-alt me-2 text-primary"></i>
                  {{ $systemSettings['contact_address'] }}</p>
              @endif
            </div>
          @endif

        </div>

        <div class="col-lg-8">
          <div class="row links-wrap">
            <div class="col-6 col-sm-6 col-md-4">
              <ul class="list-unstyled">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li><a href="{{ route('shop') }}">Shop</a></li>
                <li><a href="{{ route('cart.index') }}">Keranjang</a></li>
              </ul>
            </div>

            @auth
              <div class="col-6 col-sm-6 col-md-4">
                <ul class="list-unstyled">
                  <li><a href="{{ route('customer.dashboard') }}">Dashboard</a></li>
                  <li><a href="{{ route('addresses.index') }}">Alamat Saya</a></li>
                  <li><a href="{{ route('wishlist.index') }}">Wishlist</a></li>
                </ul>
              </div>

              <div class="col-6 col-sm-6 col-md-4">
                <ul class="list-unstyled">
                  <li><a href="{{ route('profile.show') }}">Profil</a></li>
                  <li><a href="{{ route('checkout.index') }}">Checkout</a></li>
                  <li>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                      @csrf
                      <button type="submit"
                        style="background: none; border: none; color: inherit; text-decoration: underline; cursor: pointer; padding: 0; font: inherit;">
                        Logout
                      </button>
                    </form>
                  </li>
                </ul>
              </div>
            @else
              <div class="col-6 col-sm-6 col-md-4">
                <ul class="list-unstyled">
                  <li><a href="{{ route('login') }}">Masuk</a></li>
                  <li><a href="{{ route('register') }}">Daftar</a></li>
                  <li><a href="{{ route('password.request') }}">Lupa Password</a></li>
                </ul>
              </div>
            @endauth
          </div>

          @if(isset($systemSettings) && $systemSettings['business_hours'])
            <div class="business-hours mt-4 p-3 bg-light rounded">
              <h6 class="text-primary mb-3"><i class="fas fa-clock me-2"></i>Jam Operasional</h6>
              <div class="row">
                @php
                  $businessHours = is_string($systemSettings['business_hours'])
                    ? json_decode($systemSettings['business_hours'], true)
                    : $systemSettings['business_hours'];
                @endphp
                @if($businessHours)
                  <div class="col-md-6">
                    <small class="d-block">Senin - Jumat: {{ $businessHours['monday'] ?? '09:00-18:00' }}</small>
                    <small class="d-block">Sabtu: {{ $businessHours['saturday'] ?? '09:00-15:00' }}</small>
                    <small class="d-block">Minggu: {{ $businessHours['sunday'] ?? 'Tutup' }}</small>
                  </div>
                @endif
              </div>
            </div>
          @endif
        </div>
      </div>

      <div class="border-top copyright">
        <div class="row pt-4">
          <div class="col-lg-6">
            <p class="mb-2 text-center text-lg-start">
              Copyright &copy; {{ date('Y') }}
              @if(isset($systemSettings) && $systemSettings['app_name'])
                {{ $systemSettings['app_name'] }}
              @else
                Fashion Marketplace
              @endif
              . All Rights Reserved.
            </p>
          </div>
          <div class="col-lg-6 text-center text-lg-end">
            <ul class="list-unstyled d-inline-flex ms-auto">
              <li class="me-4"><a href="#">Syarat &amp; Ketentuan</a></li>
              <li><a href="#">Kebijakan Privasi</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </footer>
  <!-- End Footer Section -->

  <!-- Back to Top Button -->
  <a href="#" class="back-to-top" id="backToTop">
    <i class="fas fa-chevron-up"></i>
  </a>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="{{ asset('furni-1.0.0/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('furni-1.0.0/js/tiny-slider.js') }}"></script>
  <script src="{{ asset('furni-1.0.0/js/custom.js') }}"></script>
  <!-- Notiflix JS -->
  <script src="{{ asset('notiflix-Notiflix-67ba12d/dist/notiflix-aio-3.2.8.min.js') }}"></script>

  <!-- Cart Counter Script -->
  @auth
    <script>
      $(document).ready(function () {
        // Load cart count on page load
        loadCartCount();
      });

      function loadCartCount() {
        $.ajax({
          url: '{{ route("cart.count") }}',
          method: 'GET',
          success: function (response) {
            if (response.success) {
              updateCartCounter(response.count);
            }
          },
          error: function (xhr) {
            console.error('Error loading cart count:', xhr.responseText);
          }
        });
      }

      function updateCartCounter(count) {
        const cartCounter = document.getElementById('cartCounter');
        if (cartCounter) {
          cartCounter.textContent = count;
          cartCounter.style.display = count > 0 ? 'flex' : 'none';
        }
      }

      // Make updateCartCounter globally available
      window.updateCartCounter = updateCartCounter;
    </script>
  @endauth

  <!-- Back to Top Script -->
  <script>
    $(document).ready(function () {
      // Show/hide back to top button
      $(window).scroll(function () {
        if ($(this).scrollTop() > 300) {
          $('#backToTop').fadeIn();
        } else {
          $('#backToTop').fadeOut();
        }
      });

      // Smooth scroll to top
      $('#backToTop').click(function (e) {
        e.preventDefault();
        $('html, body').animate({ scrollTop: 0 }, 600);
        return false;
      });
    });
  </script>

  @yield('scripts')
</body>

</html>