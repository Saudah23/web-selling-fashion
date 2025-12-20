@extends('home.layout')

@section('title', 'Katalog - FASHION SAAZZ')

@section('content')

  <!-- Breadcrumb or Page Header (Optional) -->
  @if(request('category') || request('search') || request('wishlist'))
    <div class="page-header-bar">
      <div class="container">
        <div class="page-header">
          @if(request('wishlist'))
            <h4><i class="fas fa-heart text-danger me-2"></i>Wishlist Saya</h4>
          @elseif(request('search'))
            <h4>Hasil pencarian: "{{ request('search') }}"</h4>
          @elseif(request('category'))
            @php
              $currentCategory = collect($globalCategories->pluck('children')->flatten())->firstWhere('id', request('category'));
            @endphp
            @if($currentCategory)
              <h4>{{ $currentCategory->name }}</h4>
            @endif
          @endif
        </div>
      </div>
    </div>
  @endif

  <!-- Products Section -->
  <div class="products-section">
    <div class="container">

      <!-- Search and Sort Bar -->
      <div class="controls-bar">
        <div class="controls-wrapper">
          <!-- Search Form -->
          <form method="GET" action="{{ route('shop') }}" class="search-form">
            <div class="search-wrapper">
              <input type="text" name="search" class="search-input" placeholder="Cari produk..."
                value="{{ request('search') }}">
              <button type="submit" class="search-btn">
                <i class="fas fa-search"></i>
              </button>
            </div>
            @if(request('category'))
              <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            @if(request('wishlist'))
              <input type="hidden" name="wishlist" value="{{ request('wishlist') }}">
            @endif
            @if(request('sort'))
              <input type="hidden" name="sort" value="{{ request('sort') }}">
            @endif
          </form>

          <!-- Results Info -->
          <div class="results-info">
            {{ $products->total() }} {{ request('wishlist') ? 'Item Wishlist' : 'Produk' }}
          </div>

          <!-- Sort Dropdown -->
          <form method="GET" action="{{ route('shop') }}" id="sortForm" class="sort-form">
            @if(request('search'))
              <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
            @if(request('category'))
              <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            @if(request('wishlist'))
              <input type="hidden" name="wishlist" value="{{ request('wishlist') }}">
            @endif
            <select name="sort" class="sort-select" onchange="document.getElementById('sortForm').submit()">
              <option value="">Urutkan: Terpopuler</option>
              <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama A-Z</option>
              <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Nama Z-A</option>
              <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Harga Terendah</option>
              <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
            </select>
          </form>
        </div>
      </div>

      <!-- Products Grid -->
      <div class="row">
        @forelse($products as $product)
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
                    <button class="btn btn-cart" onclick="addToCartFromShop({{ $product->id }}, this)"
                      data-product-id="{{ $product->id }}">
                      <i class="fas fa-shopping-cart"></i>
                      <span class="btn-text">Tambah ke Keranjang</span>
                    </button>
                  @else
                    <button class="btn btn-cart" disabled>
                      <i class="fas fa-times"></i>
                      <span class="btn-text">Stok Habis</span>
                    </button>
                    </button>
                  @endif
                </div>
              </div>
            </div>
          </div>
        @empty
          <!-- No Products Found -->
          <div class="col-12">
            <div class="no-products text-center py-5">
              <div class="mb-4">
                <i class="fas fa-box-open fa-4x text-muted"></i>
              </div>
              <h3 class="text-muted mb-3">
                @if(request('wishlist'))
                  Wishlist Anda Kosong
                @else
                  Tidak Ada Produk
                @endif
              </h3>
              <p class="text-muted mb-4">
                @if(request('wishlist'))
                  Anda belum menambahkan produk ke wishlist.
                @elseif(request()->hasAny(['search', 'category']))
                  Tidak ditemukan produk yang sesuai dengan pencarian Anda.
                @else
                  Belum ada produk tersedia saat ini.
                @endif
              </p>
              @if(request()->hasAny(['search', 'category', 'wishlist']))
                <a href="{{ route('shop') }}" class="btn btn-primary">
                  <i class="fas fa-arrow-left me-2"></i>Lihat Semua Produk
                </a>
              @endif
            </div>
          </div>
        @endforelse
      </div>

      <!-- Pagination -->
      @if($products->hasPages())
        <div class="row mt-4">
          <div class="col-12">
            <nav aria-label="Product pagination">
              <div class="d-flex justify-content-center">
                <div class="pagination-minimal">
                  {{ $products->withQueryString()->links('pagination::bootstrap-4') }}
                </div>
              </div>
            </nav>
          </div>
        </div>
      @endif

    </div>
  </div>

@endsection

@section('styles')
  <style>
    /* Page Header Bar */
    .page-header-bar {
      background: linear-gradient(135deg, #ff6b6b 0%, #ff8e8e 100%);
      padding: 20px 0;
      margin-bottom: 0;
    }

    .page-header h4 {
      color: white;
      margin: 0;
      font-weight: 600;
      text-align: center;
    }

    .page-header h4 i {
      font-size: 1.2em;
    }

    /* Products Section */
    .products-section {
      padding: 60px 0;
      background: #f8f9fa;
    }

    /* Controls Bar */
    .controls-bar {
      margin-bottom: 30px;
    }

    .controls-wrapper {
      display: flex;
      align-items: center;
      gap: 25px;
      flex-wrap: wrap;
    }

    .search-form {
      flex: 1;
      min-width: 250px;
    }

    .search-wrapper {
      position: relative;
      display: flex;
    }

    .search-input {
      flex: 1;
      border: 1px solid #e9ecef;
      border-radius: 6px 0 0 6px;
      padding: 10px 15px;
      font-size: 14px;
      outline: none;
      background: white;
      border-right: none;
    }

    .search-input:focus {
      border-color: #ff6b6b;
      box-shadow: 0 0 0 2px rgba(255, 107, 107, 0.1);
    }

    .search-input::placeholder {
      color: #9ca3af;
    }

    .search-btn {
      background: #ff6b6b;
      border: 1px solid #ff6b6b;
      border-radius: 0 6px 6px 0;
      color: white;
      padding: 10px 15px;
      cursor: pointer;
      font-size: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      min-width: 45px;
    }

    .search-btn:hover {
      background: #ff5252;
      border-color: #ff5252;
    }

    .results-info {
      font-weight: 500;
      color: #6b7280;
      font-size: 14px;
      white-space: nowrap;
    }

    .sort-form {
      flex-shrink: 0;
    }

    .sort-select {
      border: 1px solid #e9ecef;
      border-radius: 6px;
      padding: 10px 15px;
      font-size: 14px;
      min-width: 180px;
      outline: none;
      background: white;
      transition: all 0.2s ease;
    }

    .sort-select:focus {
      border-color: #ff6b6b;
      box-shadow: 0 0 0 2px rgba(255, 107, 107, 0.1);
    }

    /* Product Cards */
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
    }

    .wishlist-btn:hover {
      background: white;
      color: #ff6b6b;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
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
      margin-bottom: 2px;
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

    /* No Products */
    .no-products {
      padding: 80px 20px;
    }

    .no-products i {
      opacity: 0.5;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .controls-wrapper {
        flex-direction: column;
        gap: 15px;
      }

      .search-form {
        min-width: auto;
        width: 100%;
      }

      .results-info {
        order: 2;
        text-align: center;
      }

      .sort-form {
        order: 3;
        width: 100%;
      }

      .sort-select {
        width: 100%;
      }
    }


    /* Mobile optimization for 2 cards per row */
    @media (max-width: 575.98px) {
      .products-section .container {
        padding-left: 10px;
        padding-right: 10px;
      }

      .row {
        margin-left: -5px;
        margin-right: -5px;
      }

      .col-6 {
        padding-left: 5px;
        padding-right: 5px;
      }

      .product-card {
        padding: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
      }

      .product-image {
        margin-bottom: 12px;
      }

      .product-name {
        font-size: 13px;
        margin-bottom: 2px;
        height: 32px;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
      }

      .product-price {
        margin-bottom: 8px;
      }

      .current-price {
        font-size: 12px;
        font-weight: 600;
      }

      .original-price {
        font-size: 10px;
      }

      .product-actions {
        flex-direction: column;
        gap: 6px;
        margin-top: 10px;
      }

      .btn-cart {
        width: 100%;
        padding: 6px 8px;
        font-size: 11px;
      }

      .btn-cart i {
        font-size: 10px;
      }

      .btn-cart .btn-text {
        font-size: 10px;
      }

      .btn-wishlist {
        width: 32px;
        height: 32px;
        align-self: center;
        font-size: 12px;
      }

      .discount-badge {
        top: 8px;
        left: 8px;
        padding: 3px 6px;
        font-size: 10px;
      }

      .wishlist-btn {
        top: 8px;
        right: 8px;
        width: 32px;
        height: 32px;
        font-size: 12px;
      }
    }

    /* Minimal Pagination */
    .pagination-minimal .pagination {
      gap: 5px;
    }

    .pagination-minimal .page-link {
      border: 1px solid #e9ecef;
      color: #6c757d;
      padding: 8px 12px;
      border-radius: 6px;
      font-size: 14px;
      background: white;
    }

    .pagination-minimal .page-link:hover {
      background: #f8f9fa;
      border-color: #ff6b6b;
      color: #ff6b6b;
    }

    .pagination-minimal .page-item.active .page-link {
      background: #ff6b6b;
      border-color: #ff6b6b;
      color: white;
    }

    .pagination-minimal .page-item.disabled .page-link {
      background: #f8f9fa;
      border-color: #e9ecef;
      color: #adb5bd;
    }

    /* Additional fixes for dropdown positioning */
    .dropdown-menu.show {
      display: block !important;
    }

    /* Ensure category dropdowns work properly on all devices */
    .category-item.dropdown .dropdown-menu {
      will-change: transform;
    }

    /* Product Actions */
    .product-actions {
      display: flex;
      gap: 10px;
      align-items: center;
      margin-top: auto;
    }

    .btn-cart {
      width: 100%;
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

    .btn-cart:hover:not(:disabled) {
      background: #ff5252;
      color: white;
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(255, 107, 107, 0.3);
    }

    .btn-cart:disabled {
      background: #6c757d;
      color: white;
      cursor: not-allowed;
      transform: none;
      box-shadow: none;
    }

    .btn-cart i {
      font-size: 11px;
    }

    .btn-cart .btn-text {
      font-size: 11px;
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

    .stock-status {
      margin-bottom: 8px;
    }

    .stock-status small {
      font-size: 11px;
      font-weight: 500;
      padding: 2px 6px;
      border-radius: 4px;
      display: inline-block;
    }

    .stock-status.low-stock small {
      background: #fff3cd;
      color: #856404;
      border: 1px solid #ffeeba;
    }

    .stock-status.out-of-stock small {
      background: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }

    /* Mobile responsive for product cards */
    @media (max-width: 768px) {
      /* Remove dropdown overrides - handled by main layout */

      .products-section {
        padding: 40px 0;
      }

      .controls-bar {
        margin-bottom: 20px;
        padding: 0 15px;
      }

      .page-header-bar {
        padding: 15px 0;
      }

      .page-header h4 {
        font-size: 1.5rem;
      }

      .product-card {
        padding: 8px;
      }

      .product-name {
        font-size: 14px;
        margin-bottom: 2px;
      }

      .current-price {
        font-size: 12px;
      }

      .original-price {
        font-size: 10px;
      }

      .discount-badge {
        top: 8px;
        left: 8px;
        padding: 4px 8px;
        font-size: 11px;
      }

      .wishlist-btn {
        top: 8px;
        right: 8px;
        width: 32px;
        height: 32px;
        font-size: 12px;
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

      .product-actions {
        gap: 6px;
        margin-top: 10px;
      }
    }
  </style>

@endsection

@section('scripts')
  <script>
    // Wait for document ready
    $(document).ready(function () {
      // CSRF Token setup
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      // Add click event to all wishlist buttons
      $('.wishlist-btn').on('click', function (e) {
        e.preventDefault();
        const productId = $(this).data('product-id');
        toggleWishlist(productId);
      });

      @auth
        // Check wishlist status on page load for authenticated users
        const productIds = [];
        $('.wishlist-btn').each(function () {
          productIds.push($(this).data('product-id'));
        });

        if (productIds.length > 0) {
          $.ajax({
            url: '{{ route("wishlist.check") }}',
            method: 'POST',
            data: {
              product_ids: productIds
            },
            success: function (response) {
              if (response.success) {
                response.wishlisted.forEach(function (productId) {
                  updateHeartIcon(productId, true);
                });
              }
            },
            error: function (xhr) {
              console.error('Error checking wishlist status:', xhr.responseText);
            }
          });
        }
      @endauth
  });

    // Toggle wishlist function
    function toggleWishlist(productId) {
      @guest
        // Redirect to login if not authenticated
        window.location.href = '{{ route("login") }}';
        return;
      @endguest

      const button = $(`.wishlist-btn[data-product-id="${productId}"]`);

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
          console.log('Wishlist response:', response);
          if (response.success) {
            const isAdded = response.action === 'added';
            updateHeartIcon(productId, isAdded);

            // Show notification
            if (isAdded) {
              showNotification('success', 'Added to wishlist');
            } else {
              showNotification('info', 'Removed from wishlist');
            }
          }
        },
        error: function (xhr) {
          console.error('Wishlist error:', xhr);
          showNotification('error', 'Error updating wishlist');
        },
        complete: function () {
          // Re-enable button
          button.prop('disabled', false);
        }
      });
    }

    // Update heart icon appearance
    function updateHeartIcon(productId, isWishlisted) {
      const heartIcon = $(`#heart-${productId}`);
      if (heartIcon.length) {
        if (isWishlisted) {
          heartIcon.removeClass('far').addClass('fas').css('color', '#ff6b6b');
        } else {
          heartIcon.removeClass('fas').addClass('far').css('color', '');
        }
      }
    }

    // Add to cart from shop page
    function addToCartFromShop(productId, buttonElement) {
      @guest
        // Redirect to login if not authenticated
        window.location.href = '{{ route("login") }}';
        return;
      @endguest

      // Disable button and show loading
      const btn = $(buttonElement);
      const originalContent = btn.html();
      btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> <span class="btn-text">Adding...</span>');

      $.post('{{ route("cart.add") }}', {
        product_id: productId,
        quantity: 1,
        _token: $('meta[name="csrf-token"]').attr('content')
      })
        .done(function (response) {
          if (response.success) {
            showNotification('success', response.message);
            // Update cart count if cart count element exists
            if (typeof updateCartCount === 'function') {
              updateCartCount(response.cart_count);
            }
          } else {
            showNotification('error', response.message);
          }
        })
        .fail(function (xhr) {
          const errorMessage = xhr.responseJSON?.message || 'Failed to add product to cart';
          showNotification('error', errorMessage);
        })
        .always(function () {
          // Re-enable button
          btn.prop('disabled', false).html(originalContent);
        });
    }

    // Show notification
    function showNotification(type, message) {
      if (typeof Notiflix !== 'undefined') {
        switch (type) {
          case 'success':
            Notiflix.Notify.success(message);
            break;
          case 'info':
            Notiflix.Notify.info(message);
            break;
          case 'error':
            Notiflix.Notify.failure(message);
            break;
        }
      } else {
        alert(message);
      }
    }
  </script>
@endsection