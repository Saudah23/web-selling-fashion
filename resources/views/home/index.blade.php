@extends('home.layout')

@section('title', 'Beranda - FASHION SAAZZ')

@section('content')

  @php
    $customBanners = [
      [
        'title' => 'Selamat Datang di',
        'subtitle' => 'FASHION SAAZZ',
        'description' => 'Temukan koleksi fashion terbaik dengan kualitas premium. Tampil stylish dan percaya diri setiap hari bersama Fashion Saaz.',
        'image' => 'furni-1.0.0/images/hero-fashion.png',
        'button_text' => 'Belanja Sekarang',
        'url' => route('shop')
      ],
      [
        'title' => 'Koleksi Terbaru',
        'subtitle' => 'Musim Ini',
        'description' => 'Dapatkan penawaran eksklusif untuk produk terbaru kami. Desain modern yang cocok untuk gaya hidup Anda.',
        'image' => 'furni-1.0.0/images/hero-fashion.png',
        'button_text' => 'Lihat Katalog',
        'url' => route('shop')
      ]
    ];
  @endphp

  <!-- Start Banner Slider Section -->
  <div class="hero-slider">
    <div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
      <div class="carousel-indicators">
        @foreach($customBanners as $key => $banner)
          <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="{{ $key }}"
            class="{{ $key === 0 ? 'active' : '' }}" aria-current="{{ $key === 0 ? 'true' : 'false' }}"
            aria-label="Slide {{ $key + 1 }}"></button>
        @endforeach
      </div>

      <div class="carousel-inner">
        @foreach($customBanners as $key => $banner)
          <div class="carousel-item {{ $key === 0 ? 'active' : '' }}">
            <div class="hero" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);">
              <div class="container">
                <div class="row justify-content-between align-items-center">
                  <div class="col-lg-6">
                    <div class="intro-excerpt">
                      <h1 style="color: #2d3748;">
                        {{ $banner['title'] }}
                        <span class="d-block" style="color: #ff6b6b;">{{ $banner['subtitle'] }}</span>
                      </h1>
                      <p class="mb-4" style="color: #718096;">{{ $banner['description'] }}</p>
                      <p>
                        <a href="{{ $banner['url'] }}" class="btn btn-banner-primary me-2">
                          {{ $banner['button_text'] }}
                        </a>
                      </p>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="hero-img-wrap">
                      <img src="{{ asset($banner['image']) }}" class="img-fluid banner-image"
                        alt="{{ $banner['title'] }}">
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    </div>
  </div>
  <!-- End Banner/Hero Section -->

  <!-- Start Product Section -->
  <div class="product-section bg-light" id="products">
    <div class="container">
      <div class="row">

        <!-- Start Column 1 -->
        <div class="col-12 col-md-12 col-lg-3 mb-4 mb-lg-0">
          <div class="crafted-section-content">
            <h2 class="mb-3 section-title">Dibuat dengan Bahan Berkualitas</h2>
            <p class="mb-4">Produk kami dibuat dari bahan pilihan berkualitas tinggi. Setiap produk dirancang dengan
              cermat untuk memberikan keindahan dan kualitas terbaik.</p>
            <p><a href="{{ route('shop') }}" class="btn">Lihat Semua Produk</a></p>
          </div>
        </div>
        <!-- End Column 1 -->

        @forelse($featuredProducts->take(3) as $product)
          <!-- Product {{ $loop->iteration }} -->
          <div class="col-6 col-md-4 col-lg-3 mb-4">
            <div class="product-card">
              <!-- Discount Badge -->
              @if($product->compare_price && $product->compare_price > $product->price)
                @php
                  $discount = round((($product->compare_price - $product->price) / $product->compare_price) * 100);
                @endphp
                <div class="discount-badge">{{ $discount }}%</div>
              @else
                <div class="discount-badge">50%</div>
              @endif

              <!-- Wishlist Button -->
              <button class="wishlist-btn" type="button" data-product-id="{{ $product->id }}">
                <i class="far fa-heart" id="heart-{{ $product->id }}"></i>
              </button>

              <!-- Product Image -->
              <div class="product-image">
                <a href="{{ route('product.detail', $product->id) }}">
                  @php
                    $primaryImage = $product->images->where('is_primary', true)->first();
                    if (!$primaryImage) {
                      $primaryImage = $product->images->first();
                    }
                  @endphp
                  @if($primaryImage)
                    <img src="{{ asset($primaryImage->url) }}" class="img-fluid" alt="{{ $product->name }}"
                      onerror="this.src='{{ asset('furni-1.0.0/images/product-1.png') }}';">
                  @else
                    <img src="{{ asset('furni-1.0.0/images/product-1.png') }}" class="img-fluid" alt="{{ $product->name }}">
                  @endif
                </a>
              </div>

              <!-- Product Info -->
              <div class="product-info">
                <h5 class="product-name">
                  <a href="{{ route('product.detail', $product->id) }}">{{ Str::limit($product->name, 15) }}</a>
                </h5>

                <div class="product-price">
                  @if($product->compare_price && $product->compare_price > $product->price)
                    <span class="original-price">Rp{{ number_format($product->compare_price, 0, ',', '.') }}</span>
                  @else
                    <span class="original-price">Rp{{ number_format($product->price * 2, 0, ',', '.') }}</span>
                  @endif
                  <span class="current-price">Rp{{ number_format($product->price, 0, ',', '.') }}</span>
                </div>

                <!-- Stock Status -->
                @if($product->stock_quantity <= $product->low_stock_threshold && $product->stock_quantity > 0)
                  <div class="stock-status low-stock">
                    <small>Stok Terbatas</small>
                  </div>
                @elseif($product->stock_quantity == 0)
                  <div class="stock-status out-of-stock">
                    <small>Stok Habis</small>
                  </div>
                @endif

                <!-- Action Buttons -->
                <div class="product-actions mt-2">
                  @if($product->stock_quantity > 0)
                    <button class="btn btn-cart" onclick="addToCartFromHome({{ $product->id }}, this)"
                      data-product-id="{{ $product->id }}">
                      <i class="fas fa-shopping-cart"></i>
                      <span class="btn-text">Tambah ke Keranjang</span>
                    </button>
                  @else
                    <button class="btn btn-cart" disabled>
                      <i class="fas fa-times"></i>
                      <span class="btn-text">Stok Habis</span>
                    </button>
                  @endif

                  <!-- Wishlist Button -->
                  <button class="btn btn-wishlist" data-product-id="{{ $product->id }}">
                    <i class="far fa-heart" id="heart-small-{{ $product->id }}"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
          <!-- End Product {{ $loop->iteration }} -->
        @empty
          <!-- Default Products when no products available -->
          <div class="col-6 col-md-4 col-lg-3 mb-4">
            <a class="product-item" href="#">
              <img src="{{ asset('furni-1.0.0/images/product-1.png') }}" class="img-fluid product-thumbnail">
              <h3 class="product-title">Nordic Chair</h3>
              <strong class="product-price">Rp 750.000</strong>
              <span class="icon-cross">
                <img src="{{ asset('furni-1.0.0/images/cross.svg') }}" class="img-fluid">
              </span>
            </a>
          </div>

          <div class="col-6 col-md-4 col-lg-3 mb-4">
            <a class="product-item" href="#">
              <img src="{{ asset('furni-1.0.0/images/product-2.png') }}" class="img-fluid product-thumbnail">
              <h3 class="product-title">Kruzo Aero Chair</h3>
              <strong class="product-price">Rp 780.000</strong>
              <span class="icon-cross">
                <img src="{{ asset('furni-1.0.0/images/cross.svg') }}" class="img-fluid">
              </span>
            </a>
          </div>

          <div class="col-6 col-md-4 col-lg-3 mb-4">
            <a class="product-item" href="#">
              <img src="{{ asset('furni-1.0.0/images/product-3.png') }}" class="img-fluid product-thumbnail">
              <h3 class="product-title">Ergonomic Chair</h3>
              <strong class="product-price">Rp 430.000</strong>
              <span class="icon-cross">
                <img src="{{ asset('furni-1.0.0/images/cross.svg') }}" class="img-fluid">
              </span>
            </a>
          </div>
        @endforelse

      </div>
    </div>
  </div>
  <!-- End Product Section -->

  @if($featuredProducts->count() > 3)
    <!-- Start More Products Section -->
    <div class="product-section bg-light pt-0">
      <div class="container">
        <div class="row">
          <div class="col-12 text-center mb-5">
            <h2 class="section-title">Produk Unggulan Lainnya</h2>
          </div>

          @foreach($featuredProducts->slice(3)->take(4) as $product)
            <div class="col-6 col-md-6 col-lg-3 mb-4">
              <div class="product-card">
                <!-- Discount Badge -->
                @if($product->compare_price && $product->compare_price > $product->price)
                  @php
                    $discount = round((($product->compare_price - $product->price) / $product->compare_price) * 100);
                  @endphp
                  <div class="discount-badge">{{ $discount }}%</div>
                @else
                  <div class="discount-badge">50%</div>
                @endif

                <!-- Wishlist Button -->
                <button class="wishlist-btn" type="button" data-product-id="{{ $product->id }}">
                  <i class="far fa-heart" id="heart-more-{{ $product->id }}"></i>
                </button>

                <!-- Product Image -->
                <div class="product-image">
                  <a href="{{ route('product.detail', $product->id) }}">
                    @php
                      $primaryImage = $product->images->where('is_primary', true)->first();
                      if (!$primaryImage) {
                        $primaryImage = $product->images->first();
                      }
                    @endphp
                    @if($primaryImage)
                      <img src="{{ asset($primaryImage->url) }}" class="img-fluid" alt="{{ $product->name }}"
                        onerror="this.src='{{ asset('furni-1.0.0/images/product-1.png') }}';">
                    @else
                      <img src="{{ asset('furni-1.0.0/images/product-1.png') }}" class="img-fluid" alt="{{ $product->name }}">
                    @endif
                  </a>
                </div>

                <!-- Product Info -->
                <div class="product-info">
                  <h5 class="product-name">
                    <a href="{{ route('product.detail', $product->id) }}">{{ Str::limit($product->name, 15) }}</a>
                  </h5>

                  <div class="product-price">
                    @if($product->compare_price && $product->compare_price > $product->price)
                      <span class="original-price">Rp{{ number_format($product->compare_price, 0, ',', '.') }}</span>
                    @else
                      <span class="original-price">Rp{{ number_format($product->price * 2, 0, ',', '.') }}</span>
                    @endif
                    <span class="current-price">Rp{{ number_format($product->price, 0, ',', '.') }}</span>
                  </div>

                  <!-- Stock Status -->
                  @if($product->stock_quantity <= $product->low_stock_threshold && $product->stock_quantity > 0)
                    <div class="stock-status low-stock">
                      <small>Stok Terbatas</small>
                    </div>
                  @elseif($product->stock_quantity == 0)
                    <div class="stock-status out-of-stock">
                      <small>Stok Habis</small>
                    </div>
                  @endif

                  <!-- Action Buttons -->
                  <div class="product-actions mt-2">
                    @if($product->stock_quantity > 0)
                      <button class="btn btn-cart" onclick="addToCartFromHome({{ $product->id }}, this)"
                        data-product-id="{{ $product->id }}">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="btn-text">Tambah ke Keranjang</span>
                      </button>
                    @else
                      <button class="btn btn-cart" disabled>
                        <i class="fas fa-times"></i>
                        <span class="btn-text">Stok Habis</span>
                      </button>
                    @endif

                    <!-- Wishlist Button -->
                    <button class="btn btn-wishlist" data-product-id="{{ $product->id }}">
                      <i class="far fa-heart" id="heart-more-small-{{ $product->id }}"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
    <!-- End More Products Section -->
  @endif

  <!-- Start Why Choose Us Section -->
  <div class="why-choose-section bg-light">
    <div class="container">
      <div class="row justify-content-between">
        <div class="col-lg-6">
          <h2 class="section-title">Kenapa Pilih Kami</h2>
          <p>Kami menyediakan layanan terbaik dan produk berkualitas untuk pelanggan. Berikut keunggulan kami:</p>

          <div class="row my-5">
            <div class="col-6 col-md-6">
              <div class="feature">
                <div class="icon">
                  <i class="fas fa-shipping-fast fa-3x text-pink"></i>
                </div>
                <h3>Pengiriman Cepat</h3>
                <p>Pengiriman gratis untuk pesanan di atas nominal tertentu dengan pengiriman cepat.</p>
              </div>
            </div>

            <div class="col-6 col-md-6">
              <div class="feature">
                <div class="icon">
                  <i class="fas fa-shopping-bag fa-3x text-pink"></i>
                </div>
                <h3>Mudah Berbelanja</h3>
                <p>Pengalaman belanja yang simpel dan intuitif dengan pembayaran aman.</p>
              </div>
            </div>

            <div class="col-6 col-md-6">
              <div class="feature">
                <div class="icon">
                  <i class="fas fa-headset fa-3x text-pink"></i>
                </div>
                <h3>Layanan 24/7</h3>
                <p>Tim layanan pelanggan kami siap membantu Anda kapan saja.</p>
              </div>
            </div>

            <div class="col-6 col-md-6">
              <div class="feature">
                <div class="icon">
                  <i class="fas fa-undo-alt fa-3x text-pink"></i>
                </div>
                <h3>Pengembalian Mudah</h3>
                <p>Proses pengembalian mudah jika Anda tidak puas dengan pembelian.</p>
              </div>
            </div>

          </div>
        </div>

        <div class="col-lg-5">
          <div class="img-wrap">
            <img src="{{ asset('furni-1.0.0/images/why-choose-us-fashion.png') }}" alt="Why choose us" class="img-fluid">
          </div>
        </div>

      </div>
    </div>
  </div>
  <!-- End Why Choose Us Section -->

  <!-- Start Popular Categories Section -->
  <div class="popular-categories-section py-5">
    <div class="container">
      <div class="row">
        <div class="col-12 text-center mb-5">
          <h2 class="section-title">Kategori Populer</h2>
          <p class="text-muted">Temukan kategori produk favorit kami</p>
        </div>
      </div>
      <div class="row">
        <div class="col-6 col-md-6 col-lg-4 mb-4">
          <div class="category-showcase">
            <div class="category-icon">
              <i class="fas fa-tshirt fa-4x text-pink"></i>
            </div>
            <h4>Pakaian Pria</h4>
            <p>Koleksi fashion pria terkini</p>
            <a href="{{ route('shop', ['category' => 1]) }}" class="btn btn-outline-pink">Lihat</a>
          </div>
        </div>
        <div class="col-6 col-md-6 col-lg-4 mb-4">
          <div class="category-showcase">
            <div class="category-icon">
              <i class="fas fa-female fa-4x text-pink"></i>
            </div>
            <h4>Pakaian</h4>
            <p>Koleksi fashion wanita stylish</p>
            <a href="{{ route('shop', ['category' => 2]) }}" class="btn btn-outline-pink">Lihat</a>
          </div>
        </div>
        <div class="col-6 col-md-6 col-lg-4 mb-4">
          <div class="category-showcase">
            <div class="category-icon">
              <i class="fas fa-child fa-4x text-pink"></i>
            </div>
            <h4>Pakaian Anak</h4>
            <p>Fashion untuk si kecil</p>
            <a href="{{ route('shop', ['category' => 3]) }}" class="btn btn-outline-pink">Lihat</a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- End Popular Categories Section -->

  <!-- Start Statistics Section -->
  <div class="statistics-section bg-light py-5">
    <div class="container">
      <div class="row text-center">
        <div class="col-6 col-md-6 col-lg-3 mb-4">
          <div class="stat-item">
            <div class="stat-number">
              <i class="fas fa-users fa-2x text-pink mb-3"></i>
              <h2 class="text-pink">10K+</h2>
            </div>
            <h5>Pelanggan Puas</h5>
            <p class="text-muted">Pelanggan puas di seluruh Indonesia</p>
          </div>
        </div>
        <div class="col-6 col-md-6 col-lg-3 mb-4">
          <div class="stat-item">
            <div class="stat-number">
              <i class="fas fa-box fa-2x text-pink mb-3"></i>
              <h2 class="text-pink">5K+</h2>
            </div>
            <h5>Produk</h5>
            <p class="text-muted">Produk berkualitas tersedia</p>
          </div>
        </div>
        <div class="col-6 col-md-6 col-lg-3 mb-4">
          <div class="stat-item">
            <div class="stat-number">
              <i class="fas fa-store fa-2x text-pink mb-3"></i>
              <h2 class="text-pink">100+</h2>
            </div>
            <h5>Brand</h5>
            <p class="text-muted">Mitra brand terpercaya</p>
          </div>
        </div>
        <div class="col-6 col-md-6 col-lg-3 mb-4">
          <div class="stat-item">
            <div class="stat-number">
              <i class="fas fa-shipping-fast fa-2x text-pink mb-3"></i>
              <h2 class="text-pink">50K+</h2>
            </div>
            <h5>Pesanan Terkirim</h5>
            <p class="text-muted">Pesanan berhasil dikirim</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- End Statistics Section -->

  <!-- Start Contact & Location Section -->
  <div class="contact-location-section bg-light py-5" id="contact">
    <div class="container">
      <div class="row">
        <div class="col-12 text-center mb-5">
          <h2 class="section-title">Kunjungi Toko Kami</h2>
          <p class="text-muted">Lihat produk kami langsung di lokasi toko</p>
        </div>
      </div>
      <div class="row">
        <!-- Contact Info -->
        <div class="col-lg-6 mb-4">
          <div class="contact-info">
            <h4 class="mb-4">Hubungi Kami</h4>
            <div class="contact-item mb-3">
              <i class="fas fa-map-marker-alt fa-2x text-pink me-3"></i>
              <div>
                <h6>Alamat</h6>
                <p class="text-muted">{{ $systemSettings['contact_address'] ?? $shippingAddress['full_address'] }}</p>
              </div>
            </div>
            <div class="contact-item mb-3">
              <i class="fas fa-phone fa-2x text-pink me-3"></i>
              <div>
                <h6>Telepon</h6>
                <p class="text-muted">{{ $systemSettings['contact_phone'] ?: '+62 812-3456-7890' }}</p>
              </div>
            </div>
            <div class="contact-item mb-3">
              <i class="fas fa-envelope fa-2x text-pink me-3"></i>
              <div>
                <h6>Email</h6>
                <p class="text-muted">{{ $systemSettings['contact_email'] ?: 'info@fashionsaazz.com' }}</p>
              </div>
            </div>
            <div class="contact-item mb-3">
              <i class="fas fa-clock fa-2x text-pink me-3"></i>
              <div>
                <h6>Jam Buka</h6>
                <p class="text-muted">
                  @php
                      $businessHours = $systemSettings['business_hours'];
                      if (is_string($businessHours) && json_decode($businessHours, true)) {
                          $businessHours = json_decode($businessHours, true);
                      }
                      $dayMap = [
                          'monday' => 'Senin',
                          'tuesday' => 'Selasa',
                          'wednesday' => 'Rabu',
                          'thursday' => 'Kamis',
                          'friday' => 'Jumat',
                          'saturday' => 'Sabtu',
                          'sunday' => 'Minggu',
                      ];
                  @endphp
                  @if(is_array($businessHours))
                    @foreach($businessHours as $day => $hours)
                        <span class="d-block text-capitalize">{{ $dayMap[strtolower($day)] ?? $day }}: {{ $hours }}</span>
                    @endforeach
                  @elseif($businessHours)
                    {!! nl2br(e($businessHours)) !!}
                  @else
                    Senin - Minggu: 08:00 - 17:00
                  @endif
                </p>
              </div>
            </div>
          </div>
        </div>
        <!-- Map -->
        <div class="col-lg-6">
          <div class="map-container">
            <h4 class="mb-4">Lokasi Kami</h4>
            <div class="map-wrapper">
              <iframe
                src="https://maps.google.com/maps?q={{ urlencode($shippingAddress['city'] . ', ' . $shippingAddress['province']) }}&t=&z=13&ie=UTF8&iwloc=&output=embed"
                width="100%" height="350" style="border:0; border-radius: 15px;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
              </iframe>
            </div>
            <div class="directions-btn mt-3">
              <a href="https://maps.google.com?q={{ urlencode($shippingAddress['full_address']) }}" target="_blank"
                class="btn btn-outline-pink">
                <i class="fas fa-directions me-2"></i>Lihat Rute
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- End Contact & Location Section -->

@endsection

@section('styles')
  <style>
    /* Banner Slider Styles */
    .hero-slider {
      position: relative;
      z-index: 1;
    }

    .hero-slider .carousel-indicators {
      bottom: 20px;
      z-index: 10;
    }

    .hero-slider .carousel-indicators button {
      width: 40px;
      height: 4px;
      border-radius: 2px;
      border: none;
      background: rgba(255, 107, 107, 0.3);
      margin: 0 4px;
      transition: all 0.3s ease;
    }

    .hero-slider .carousel-indicators button.active {
      background: #ff6b6b;
      width: 50px;
    }

    /* Custom Text Pink */
    .text-pink {
      color: #ff6b6b !important;
    }
    
    .btn-outline-pink {
      color: #ff6b6b;
      border-color: #ff6b6b;
    }
    
    .btn-outline-pink:hover {
      background-color: #ff6b6b;
      color: #fff;
    }

    /* Custom Banner Button */
    .btn-banner-primary {
      background: #ff6b6b;
      border: 2px solid #ff6b6b;
      color: white;
      padding: 12px 28px;
      border-radius: 8px;
      font-weight: 600;
      font-size: 16px;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-block;
    }

    .btn-banner-primary:hover {
      background: white;
      color: #ff6b6b;
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(255, 107, 107, 0.3);
    }

    /* Banner Image Styling */
    .banner-image {
      width: 100%;
      height: 400px;
      object-fit: cover;
      object-position: center;
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      position: relative;
      z-index: 1;
    }

    .hero-img-wrap {
      position: relative;
      z-index: 1;
    }

    /* Carousel Controls */
    .hero-slider .carousel-control-prev,
    .hero-slider .carousel-control-next {
      width: 50px;
      height: 50px;
      background: rgba(255, 107, 107, 0.9);
      border-radius: 50%;
      top: 50%;
      transform: translateY(-50%);
      opacity: 0.8;
      transition: all 0.3s ease;
      z-index: 15;
    }

    .hero-slider .carousel-control-prev {
      left: 20px;
    }

    .hero-slider .carousel-control-next {
      right: 20px;
    }

    .hero-slider .carousel-control-prev:hover,
    .hero-slider .carousel-control-next:hover {
      opacity: 1;
      transform: translateY(-50%) scale(1.1);
    }

    .hero-slider .carousel-control-prev-icon,
    .hero-slider .carousel-control-next-icon {
      width: 20px;
      height: 20px;
      background-size: 20px 20px;
      background-color: white;
    }

    /* Light banner background variants */
    .hero {
      min-height: 500px;
      display: flex;
      align-items: center;
      position: relative;
      overflow: hidden;
    }

    .hero::before {
      content: '';
      position: absolute;
      top: 0;
      right: 0;
      width: 100px;
      height: 100px;
      background: rgba(255, 107, 107, 0.1);
      border-radius: 50%;
      transform: translate(30%, -30%);
    }

    .hero::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 150px;
      height: 150px;
      background: rgba(255, 107, 107, 0.05);
      border-radius: 50%;
      transform: translate(-30%, 30%);
    }

    /* Dynamic Content Sections */
    .content-section {
      position: relative;
    }

    .text-pink {
      color: #ff6b6b !important;
    }

    .feature-card {
      transition: all 0.3s ease;
      border: 1px solid #e9ecef;
    }

    .feature-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(255, 107, 107, 0.1) !important;
      border-color: #ff6b6b;
    }

    .feature-icon {
      transition: transform 0.3s ease;
    }

    .feature-card:hover .feature-icon {
      transform: scale(1.1);
    }

    /* Section Backgrounds */
    .content-section:nth-child(odd) {
      background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
    }

    .content-section:nth-child(even) {
      background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .hero {
        min-height: 400px;
        text-align: center;
      }

      .banner-image {
        height: 280px;
        border-radius: 8px;
      }

      .hero-slider .carousel-control-prev,
      .hero-slider .carousel-control-next {
        width: 40px;
        height: 40px;
      }

      .hero-slider .carousel-control-prev {
        left: 10px;
      }

      .hero-slider .carousel-control-next {
        right: 10px;
      }

      .hero-slider .carousel-control-prev-icon,
      .hero-slider .carousel-control-next-icon {
        width: 16px;
        height: 16px;
        background-size: 16px 16px;
      }

      .btn-banner-primary {
        padding: 10px 24px;
        font-size: 14px;
      }

      .feature-card {
        margin-bottom: 20px;
      }
    }

    /* Tablet Design */
    @media (max-width: 992px) and (min-width: 769px) {
      .banner-image {
        height: 350px;
      }
    }

    /* Product Cards Styling - Same as Shop Page */
    .product-card {
      background: white;
      border-radius: 15px;
      padding: 10px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
      transition: all 0.3s ease;
      position: relative;
      height: 100%;
      display: flex;
      flex-direction: column;
    }

    .product-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 12px 35px rgba(0, 0, 0, 0.15);
    }

    /* Discount Badge */
    .discount-badge {
      position: absolute;
      top: 15px;
      left: 15px;
      background: #ff6b6b;
      color: white;
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
      z-index: 2;
    }

    /* Wishlist Button */
    .wishlist-btn {
      position: absolute;
      top: 15px;
      right: 15px;
      background: rgba(255, 255, 255, 0.9);
      border: none;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #6c757d;
      font-size: 16px;
      transition: all 0.3s ease;
      z-index: 2;
      cursor: pointer;
    }

    .wishlist-btn:hover {
      background: white;
      color: #ff6b6b;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .wishlist-btn.wishlisted {
      color: #ff6b6b;
      background: white;
    }

    /* Product Image */
    .product-image {
      position: relative;
      border-radius: 12px;
      overflow: hidden;
      margin-bottom: 10px;
      background: #f8f9fa;
      aspect-ratio: 4/3;
    }

    .product-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.3s ease;
    }

    .product-card:hover .product-image img {
      transform: scale(1.05);
    }

    /* Product Info */
    .product-info {
      text-align: left;
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    .product-name {
      font-size: 16px;
      font-weight: 600;
      margin-bottom: 6px;
      line-height: 1.4;
    }

    .product-name a {
      color: #333;
      text-decoration: none;
      transition: color 0.3s ease;
    }

    .product-name a:hover {
      color: #ff6b6b;
    }

    .product-price {
      margin-bottom: 5px;
    }

    .original-price {
      color: #999;
      text-decoration: line-through;
      font-size: 14px;
      margin-right: 8px;
    }

    .current-price {
      color: #ff6b6b;
      font-weight: 700;
      font-size: 18px;
    }

    /* Stock Status */
    .stock-status.low-stock small {
      background: #ffc107;
      color: #856404;
      padding: 3px 8px;
      border-radius: 12px;
      font-size: 11px;
      font-weight: 600;
    }

    .stock-status.out-of-stock small {
      background: #dc3545;
      color: white;
      padding: 3px 8px;
      border-radius: 12px;
      font-size: 11px;
      font-weight: 600;
    }

    /* Product Actions */
    .product-actions {
      display: flex;
      gap: 10px;
      align-items: center;
      margin-top: auto;
    }

    .btn-cart {
      flex: 1;
      background: #ff6b6b;
      color: white;
      border: none;
      padding: 12px 16px;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 600;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      text-decoration: none;
    }

    .btn-cart:hover {
      background: #ff5252;
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(255, 107, 107, 0.3);
      color: white;
    }

    .btn-cart:disabled {
      background: #6c757d;
      cursor: not-allowed;
      transform: none;
      box-shadow: none;
    }

    .btn-wishlist {
      background: #f8f9fa;
      border: 1px solid #e9ecef;
      width: 45px;
      height: 45px;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #6c757d;
      font-size: 16px;
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .btn-wishlist:hover,
    .btn-wishlist.wishlisted {
      background: white;
      color: #ff6b6b;
      border-color: #ff6b6b;
      box-shadow: 0 4px 15px rgba(255, 107, 107, 0.2);
    }

    /* Mobile responsive for product cards */
    @media (max-width: 768px) {
      .product-card {
        padding: 8px;
      }

      .product-name {
        font-size: 14px;
      }

      .current-price {
        font-size: 16px;
      }

      .btn-cart {
        padding: 10px 12px;
        font-size: 13px;
      }

      .btn-cart .btn-text {
        display: none;
      }

      .btn-wishlist {
        width: 40px;
        height: 40px;
      }
    }

    /* New Sections Styling */

    /* Popular Categories Section */
    .popular-categories-section {
      background: white;
    }

    .category-showcase {
      text-align: center;
      padding: 40px 20px;
      background: white;
      border-radius: 15px;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
      transition: all 0.3s ease;
      height: 100%;
    }

    .category-showcase:hover {
      transform: translateY(-10px);
      box-shadow: 0 15px 40px rgba(255, 107, 107, 0.15);
    }

    .category-icon {
      margin-bottom: 20px;
    }

    .category-showcase h4 {
      font-weight: 600;
      margin-bottom: 15px;
      color: #2d3748;
    }

    .category-showcase p {
      color: #718096;
      margin-bottom: 25px;
      font-size: 14px;
    }

    .btn-outline-pink {
      border: 2px solid #ff6b6b;
      color: #ff6b6b;
      background: transparent;
      padding: 10px 25px;
      border-radius: 25px;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.3s ease;
    }

    .btn-outline-pink:hover {
      background: #ff6b6b;
      color: white;
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(255, 107, 107, 0.3);
    }

    /* Statistics Section */
    .statistics-section {
      background: #f8f9fa;
    }

    .stat-item {
      padding: 30px 15px;
    }

    .stat-number h2 {
      font-size: 3rem;
      font-weight: 700;
      margin-bottom: 10px;
    }

    .stat-item h5 {
      font-weight: 600;
      color: #2d3748;
      margin-bottom: 10px;
    }

    /* Contact & Location Section */
    .contact-location-section {
      background: #f8f9fa;
    }

    .contact-info {
      padding: 20px;
    }

    .contact-item {
      display: flex;
      align-items: flex-start;
      padding: 15px 0;
    }

    .contact-item i {
      flex-shrink: 0;
      margin-top: 5px;
    }

    .contact-item h6 {
      font-weight: 600;
      color: #2d3748;
      margin-bottom: 5px;
    }

    .contact-item p {
      margin-bottom: 0;
      line-height: 1.6;
    }

    .map-container {
      padding: 20px;
    }

    /* Mobile optimization for contact section */
    @media (max-width: 768px) {
      .contact-location-section {
        padding: 30px 0 !important;
      }

      .contact-info {
        padding: 10px;
      }

      .contact-item {
        padding: 8px 0;
      }

      .contact-item i {
        font-size: 18px !important;
        margin-right: 12px;
        margin-top: 2px;
      }

      .contact-item h6 {
        font-size: 14px;
        margin-bottom: 3px;
      }

      .contact-item p {
        font-size: 13px;
        line-height: 1.4;
      }

      .map-container {
        padding: 10px;
        margin-top: 20px;
      }

      .map-container h4 {
        font-size: 18px;
        margin-bottom: 15px !important;
      }

      .map-wrapper iframe {
        height: 200px !important;
      }
    }

    .map-wrapper {
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
      border-radius: 15px;
      overflow: hidden;
    }

    .directions-btn {
      text-align: center;
    }

    /* Enhanced icon styling */
    .feature .icon i,
    .category-icon i,
    .stat-item i {
      transition: all 0.3s ease;
    }

    .feature:hover .icon i,
    .category-showcase:hover .category-icon i {
      transform: scale(1.1);
    }

    /* Responsive improvements */
    @media (max-width: 768px) {
      .category-showcase {
        padding: 30px 15px;
        margin-bottom: 20px;
      }

      .stat-number h2 {
        font-size: 2.5rem;
      }

      .contact-item {
        flex-direction: row;
        text-align: left;
        padding: 8px 0;
      }

      .contact-item i {
        margin-right: 12px;
        margin-top: 2px;
      }
    }
  </style>
@endsection

@section('scripts')
  <script>
    $(document).ready(function () {
      // Check wishlist status for all products on page load
      const productIds = [];
      $('.wishlist-btn, .btn-wishlist').each(function () {
        const productId = $(this).data('product-id');
        if (productId && productIds.indexOf(productId) === -1) {
          productIds.push(productId);
        }
      });

      if (productIds.length > 0 && @json(auth()->check())) {
        checkWishlistStatus(productIds);
      }

      // Wishlist toggle functionality
      $(document).on('click', '.wishlist-btn, .btn-wishlist', function (e) {
        e.preventDefault();
        e.stopPropagation();

        @guest
          window.location.href = '{{ route("login") }}';
          return;
        @endguest

            const productId = $(this).data('product-id');
        const button = $(this);

        toggleWishlist(productId, button);
      });
    });

    // Check wishlist status for multiple products
    function checkWishlistStatus(productIds) {
      $.ajax({
        url: '{{ route("wishlist.check") }}',
        method: 'POST',
        data: {
          product_ids: productIds,
          _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
          if (response.success) {
            response.wishlisted.forEach(function (productId) {
              updateWishlistButtons(productId, true);
            });
          }
        },
        error: function (xhr) {
          console.error('Error checking wishlist status:', xhr);
        }
      });
    }

    // Toggle wishlist status
    function toggleWishlist(productId, button) {
      // Disable button during request
      button.prop('disabled', true);

      $.ajax({
        url: '{{ route("wishlist.toggle") }}',
        method: 'POST',
        data: {
          product_id: productId,
          _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
          if (response.success) {
            updateWishlistButtons(productId, response.wishlisted);

            // Show notification
            if (response.wishlisted) {
              Notiflix.Notify.success('Added to wishlist!');
            } else {
              Notiflix.Notify.success('Removed from wishlist!');
            }
          }
        },
        error: function (xhr) {
          console.error('Error toggling wishlist:', xhr);
          Notiflix.Notify.failure('Something went wrong. Please try again.');
        },
        complete: function () {
          button.prop('disabled', false);
        }
      });
    }

    // Update all wishlist buttons for a product
    function updateWishlistButtons(productId, isWishlisted) {
      // Update all buttons for this product (both top corner and action area)
      $(`[data-product-id="${productId}"]`).each(function () {
        const button = $(this);
        const heartIcon = button.find('i');

        if (isWishlisted) {
          button.addClass('wishlisted');
          heartIcon.removeClass('far fa-heart').addClass('fas fa-heart');
        } else {
          button.removeClass('wishlisted');
          heartIcon.removeClass('fas fa-heart').addClass('far fa-heart');
        }
      });

      // Also update individual heart icons with specific IDs
      const heartSelectors = [
        `#heart-${productId}`,
        `#heart-small-${productId}`,
        `#heart-more-${productId}`,
        `#heart-more-small-${productId}`
      ];

      heartSelectors.forEach(function (selector) {
        const heartIcon = $(selector);
        if (heartIcon.length > 0) {
          if (isWishlisted) {
            heartIcon.removeClass('far fa-heart').addClass('fas fa-heart');
            heartIcon.css('color', '#ff6b6b');
          } else {
            heartIcon.removeClass('fas fa-heart').addClass('far fa-heart');
            heartIcon.css('color', '');
          }
        }
      });
    }

    // Add to cart functionality
    function addToCartFromHome(productId, button) {
      @guest
        // Redirect to login if not authenticated
        window.location.href = '{{ route("login") }}';
        return;
      @endguest

        // Disable button during request
        const btn = $(button);
      const originalContent = btn.html();
      btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> <span class="btn-text">Adding...</span>');

      $.post('{{ route("cart.add") }}', {
        product_id: productId,
        quantity: 1,
        _token: $('meta[name="csrf-token"]').attr('content')
      })
        .done(function (response) {
          if (response.success) {
            Notiflix.Notify.success(response.message);
            // Update cart count if cart count element exists
            if (typeof updateCartCount === 'function') {
              updateCartCount(response.cart_count);
            }
          } else {
            Notiflix.Notify.failure(response.message);
          }
        })
        .fail(function (xhr) {
          const errorMessage = xhr.responseJSON?.message || 'Failed to add product to cart';
          Notiflix.Notify.failure(errorMessage);
        })
        .always(function () {
          // Re-enable button
          btn.prop('disabled', false).html(originalContent);
        });
    }
  </script>

  @if(!auth()->check())
    <!-- Load Notiflix for notifications -->
    <script src="https://cdn.jsdelivr.net/npm/notiflix@3.2.6/dist/notiflix-aio-3.2.6.min.js"></script>
  @endif
@endsection