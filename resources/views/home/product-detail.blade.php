@extends('home.layout')

@section('title', $product->name . ' - Marketplace')
@section('description', Str::limit($product->description, 150))

@section('content')

  <!-- Breadcrumb Section -->
  <div class="breadcrumb-section">
    <div class="container">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ route('home') }}">Home</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ route('shop') }}">Shop</a>
          </li>
          @if($product->category)
            <li class="breadcrumb-item">
              <a href="{{ route('shop', ['category' => $product->category->id]) }}">
                {{ $product->category->name }}
              </a>
            </li>
          @endif
          <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
        </ol>
      </nav>
    </div>
  </div>

  <!-- Product Detail Section -->
  <div class="product-detail-section">
    <div class="container">
      <div class="row">

        <!-- Product Images -->
        <div class="col-lg-6 mb-4">
          <div class="product-images">
            @if($product->images->isNotEmpty())
              <!-- Main Image -->
              <div class="main-image-container">
                @php
                  $primaryImage = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                @endphp
                <img id="mainProductImage"
                     src="{{ asset($primaryImage->url) }}"
                     class="main-product-image"
                     alt="{{ $product->name }}"
                     onerror="this.src='{{ asset('furni-1.0.0/images/product-1.png') }}';">
              </div>

              @if($product->images->count() > 1)
                <!-- Thumbnail Images -->
                <div class="thumbnail-container">
                  @foreach($product->images as $image)
                    <img src="{{ asset($image->url) }}"
                         class="thumbnail-img {{ $image->is_primary ? 'active' : '' }}"
                         alt="{{ $product->name }}"
                         onclick="changeMainImage('{{ asset($image->url) }}', this)"
                         onerror="this.src='{{ asset('furni-1.0.0/images/product-1.png') }}';">
                  @endforeach
                </div>
              @endif
            @else
              <!-- Default Image -->
              <div class="main-image-container">
                <img src="{{ asset('furni-1.0.0/images/product-1.png') }}"
                     class="main-product-image"
                     alt="{{ $product->name }}">
              </div>
            @endif
          </div>
        </div>

        <!-- Product Information -->
        <div class="col-lg-6">
          <div class="product-info">
            <!-- Product Header -->
            <div class="product-header">
              <h1 class="product-title">{{ $product->name }}</h1>
              @if($product->sku)
                <p class="product-sku">SKU: {{ $product->sku }}</p>
              @endif
              @if($product->category)
                <a href="{{ route('shop', ['category' => $product->category->id]) }}" class="product-category">
                  {{ $product->category->name }}
                </a>
              @endif
            </div>

            <!-- Price Section -->
            <div class="price-section">
              <div class="price-main">
                Rp{{ number_format($product->price, 0, ',', '.') }}
              </div>
              @if($product->compare_price && $product->compare_price > $product->price)
                <div class="price-compare">
                  <span class="original-price">Rp{{ number_format($product->compare_price, 0, ',', '.') }}</span>
                  <span class="discount-badge">
                    {{ round((($product->compare_price - $product->price) / $product->compare_price) * 100) }}% OFF
                  </span>
                </div>
              @endif
            </div>

            <!-- Stock Status -->
            <div class="stock-status">
              @if($product->stock_quantity > 0)
                @if($product->stock_quantity <= $product->low_stock_threshold)
                  <div class="stock-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    Only {{ $product->stock_quantity }} left in stock!
                  </div>
                @else
                  <div class="stock-available">
                    <i class="fas fa-check-circle"></i>
                    In Stock ({{ $product->stock_quantity }} available)
                  </div>
                @endif
              @else
                <div class="stock-out">
                  <i class="fas fa-times-circle"></i>
                  Out of Stock
                </div>
              @endif
            </div>

            <!-- Product Actions -->
            <div class="product-actions">
              @if($product->stock_quantity > 0)
                <!-- Quantity Selector -->
                <div class="quantity-selector">
                  <label for="quantity" class="quantity-label">Quantity:</label>
                  <div class="quantity-controls">
                    <button type="button" class="quantity-btn quantity-minus" onclick="changeQuantity(-1)">-</button>
                    <input type="number"
                           id="quantity"
                           name="quantity"
                           value="1"
                           min="1"
                           max="{{ $product->stock_quantity }}"
                           class="quantity-input">
                    <button type="button" class="quantity-btn quantity-plus" onclick="changeQuantity(1)">+</button>
                  </div>
                  <small class="quantity-note">{{ $product->stock_quantity }} available</small>
                </div>
              @endif

              <div class="action-buttons">
                @if($product->stock_quantity > 0)
                  <button class="btn btn-add-cart" type="button" onclick="addToCart({{ $product->id }})">
                    <i class="fas fa-shopping-cart"></i>
                    Add to Cart
                  </button>
                @else
                  <button class="btn btn-add-cart" disabled>
                    <i class="fas fa-times"></i>
                    Out of Stock
                  </button>
                @endif

                <!-- Wishlist Button -->
                <button class="btn btn-wishlist" type="button" data-product-id="{{ $product->id }}">
                  <i class="far fa-heart" id="heart-{{ $product->id }}"></i>
                </button>
              </div>

              <a href="{{ route('shop') }}" class="btn btn-back">
                <i class="fas fa-arrow-left"></i> Back to Shop
              </a>
            </div>

            <!-- Product Details -->
            @if($product->description || ($product->attributes && is_array($product->attributes) && count($product->attributes) > 0) || $product->weight || $product->dimensions)
              <div class="product-details">
                <h3>Product Details</h3>

                @if($product->description)
                  <div class="detail-section">
                    <h4>Description</h4>
                    <p>{!! nl2br(e($product->description)) !!}</p>
                  </div>
                @endif

                @if($product->attributes && is_array($product->attributes) && count($product->attributes) > 0)
                  <div class="detail-section">
                    <h4>Specifications</h4>
                    <div class="spec-list">
                      @foreach($product->attributes as $key => $value)
                        @if($value && !in_array(strtolower($key), ['id', 'created_at', 'updated_at']))
                          <div class="spec-item">
                            <span class="spec-label">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                            <span class="spec-value">{{ is_array($value) ? implode(', ', $value) : $value }}</span>
                          </div>
                        @endif
                      @endforeach
                    </div>
                  </div>
                @endif

                @if($product->weight || $product->dimensions)
                  <div class="detail-section">
                    <h4>Physical Properties</h4>
                    <div class="spec-list">
                      @if($product->weight)
                        <div class="spec-item">
                          <span class="spec-label">Weight:</span>
                          <span class="spec-value">{{ $product->weight }}g</span>
                        </div>
                      @endif
                      @if($product->dimensions)
                        <div class="spec-item">
                          <span class="spec-label">Dimensions:</span>
                          <span class="spec-value">{{ $product->dimensions }}</span>
                        </div>
                      @endif
                    </div>
                  </div>
                @endif
              </div>
            @endif
          </div>
        </div>

      </div>

      <!-- Related Products -->
      @if($relatedProducts->isNotEmpty())
        <div class="related-products">
          <h3>Related Products</h3>
          <div class="related-grid">
            @foreach($relatedProducts as $relatedProduct)
              <div class="related-card">
                <a href="{{ route('product.detail', $relatedProduct->id) }}" class="related-link">
                  <div class="related-image">
                    @if($relatedProduct->images->isNotEmpty())
                      <img src="{{ asset($relatedProduct->images->first()->url) }}"
                           alt="{{ $relatedProduct->name }}"
                           onerror="this.src='{{ asset('furni-1.0.0/images/product-1.png') }}';">
                    @else
                      <img src="{{ asset('furni-1.0.0/images/product-1.png') }}"
                           alt="{{ $relatedProduct->name }}">
                    @endif
                  </div>
                  <div class="related-info">
                    <h4>{{ $relatedProduct->name }}</h4>
                    <div class="related-price">
                      @if($relatedProduct->compare_price && $relatedProduct->compare_price > $relatedProduct->price)
                        <span class="original-price">Rp{{ number_format($relatedProduct->compare_price, 0, ',', '.') }}</span>
                      @endif
                      <span class="current-price">Rp{{ number_format($relatedProduct->price, 0, ',', '.') }}</span>
                    </div>
                  </div>
                </a>
              </div>
            @endforeach
          </div>
        </div>
      @endif

    </div>
  </div>
  <!-- End Product Detail Section -->

@endsection

@section('scripts')
<script>
// Wait for document ready
$(document).ready(function() {
    // CSRF Token setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Add click event to wishlist button
    $('.btn-wishlist').on('click', function(e) {
        e.preventDefault();
        const productId = $(this).data('product-id');
        toggleWishlist(productId);
    });

    @auth
    // Check wishlist status on page load for authenticated users
    const productId = $('.btn-wishlist').data('product-id');
    if (productId) {
        $.ajax({
            url: '{{ route("wishlist.check") }}',
            method: 'POST',
            data: {
                product_ids: [productId]
            },
            success: function(response) {
                if (response.success && response.wishlisted.includes(productId)) {
                    updateHeartIcon(productId, true);
                }
            },
            error: function(xhr) {
                console.error('Error checking wishlist status:', xhr.responseText);
            }
        });
    }
    @endauth

    // Initialize quantity buttons
    updateQuantityButtons();

    // Add event listener for manual quantity input changes
    const quantityInput = document.getElementById('quantity');
    if (quantityInput) {
        quantityInput.addEventListener('input', function() {
            const value = parseInt(this.value);
            const min = parseInt(this.getAttribute('min'));
            const max = parseInt(this.getAttribute('max'));

            if (value < min) {
                this.value = min;
            } else if (value > max) {
                this.value = max;
            }

            updateQuantityButtons();
        });
    }
});

// Change main image function
function changeMainImage(imageSrc, thumbnail) {
    // Update main image
    const mainImage = document.getElementById('mainProductImage');
    mainImage.src = imageSrc;

    // Remove active class from all thumbnails
    document.querySelectorAll('.thumbnail-img').forEach(img => {
        img.classList.remove('active');
    });

    // Add active class to clicked thumbnail
    thumbnail.classList.add('active');
}

// Quantity control functions
function changeQuantity(change) {
    const quantityInput = document.getElementById('quantity');
    const currentValue = parseInt(quantityInput.value);
    const maxValue = parseInt(quantityInput.getAttribute('max'));
    const minValue = parseInt(quantityInput.getAttribute('min'));

    const newValue = currentValue + change;

    if (newValue >= minValue && newValue <= maxValue) {
        quantityInput.value = newValue;

        // Update button states
        updateQuantityButtons();
    }
}

function updateQuantityButtons() {
    const quantityInput = document.getElementById('quantity');
    const minusBtn = document.querySelector('.quantity-minus');
    const plusBtn = document.querySelector('.quantity-plus');

    const currentValue = parseInt(quantityInput.value);
    const maxValue = parseInt(quantityInput.getAttribute('max'));
    const minValue = parseInt(quantityInput.getAttribute('min'));

    // Disable minus button if at minimum
    minusBtn.disabled = currentValue <= minValue;

    // Disable plus button if at maximum
    plusBtn.disabled = currentValue >= maxValue;
}

// Add to cart function
function addToCart(productId) {
    @guest
        // Redirect to login if not authenticated
        window.location.href = '{{ route("login") }}';
        return;
    @endguest

    const quantityInput = document.getElementById('quantity');
    const quantity = parseInt(quantityInput.value);
    const addToCartBtn = document.querySelector('.btn-add-cart');

    if (!quantity || quantity < 1) {
        showNotification('error', 'Please select a valid quantity');
        return;
    }

    // Disable button during request
    addToCartBtn.disabled = true;
    addToCartBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';

    $.ajax({
        url: '{{ route("cart.add") }}',
        method: 'POST',
        data: {
            product_id: productId,
            quantity: quantity,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                showNotification('success', response.message);

                // Update cart counter if it exists
                updateCartCounter(response.cart_count);

                // Optionally reset quantity to 1
                quantityInput.value = 1;
                updateQuantityButtons();
            } else {
                showNotification('error', response.message);
            }
        },
        error: function(xhr) {
            console.error('Add to cart error:', xhr);
            const response = xhr.responseJSON;
            const message = response?.message || 'Failed to add product to cart';
            showNotification('error', message);
        },
        complete: function() {
            // Re-enable button
            addToCartBtn.disabled = false;
            addToCartBtn.innerHTML = '<i class="fas fa-shopping-cart"></i> Add to Cart';
        }
    });
}

// Update cart counter in navigation
function updateCartCounter(count) {
    const cartCounter = document.querySelector('.cart-counter');
    if (cartCounter) {
        cartCounter.textContent = count;
        cartCounter.style.display = count > 0 ? 'inline' : 'none';
    }
}

// Toggle wishlist function
function toggleWishlist(productId) {
    @guest
        // Redirect to login if not authenticated
        window.location.href = '{{ route("login") }}';
        return;
    @endguest

    const button = $(`.btn-wishlist[data-product-id="${productId}"]`);

    // Disable button during request
    button.prop('disabled', true);

    $.ajax({
        url: '{{ route("wishlist.toggle") }}',
        method: 'POST',
        data: {
            product_id: productId,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
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
        error: function(xhr) {
            console.error('Wishlist error:', xhr);
            showNotification('error', 'Error updating wishlist');
        },
        complete: function() {
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

// Show notification
function showNotification(type, message) {
    if (typeof Notiflix !== 'undefined') {
        switch(type) {
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

@section('styles')
<style>
  /* Breadcrumb Section */
  .breadcrumb-section {
    background: #f8f9fa;
    padding: 20px 0;
    border-bottom: 1px solid #e9ecef;
  }

  .breadcrumb {
    background: none;
    padding: 0;
    margin: 0;
  }

  .breadcrumb-item a {
    color: #6c757d;
    text-decoration: none;
  }

  .breadcrumb-item a:hover {
    color: #ff6b6b;
  }

  .breadcrumb-item.active {
    color: #333;
    font-weight: 500;
  }

  /* Product Detail Section */
  .product-detail-section {
    padding: 60px 0;
    background: white;
  }

  /* Product Images */
  .product-images {
    position: sticky;
    top: 100px;
  }

  .main-image-container {
    margin-bottom: 20px;
  }

  .main-product-image {
    width: 100%;
    height: 500px;
    object-fit: cover;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  }

  .thumbnail-container {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
  }

  .thumbnail-img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
    cursor: pointer;
    border: 2px solid transparent;
    transition: all 0.3s ease;
  }

  .thumbnail-img:hover {
    opacity: 0.8;
    transform: scale(1.05);
  }

  .thumbnail-img.active {
    border-color: #ff6b6b;
  }

  /* Product Info */
  .product-info {
    padding-left: 40px;
  }

  .product-header {
    margin-bottom: 30px;
  }

  .product-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 10px;
    line-height: 1.2;
  }

  .product-sku {
    color: #6c757d;
    font-size: 14px;
    margin-bottom: 10px;
  }

  .product-category {
    color: #ff6b6b;
    text-decoration: none;
    font-weight: 500;
    font-size: 16px;
  }

  .product-category:hover {
    text-decoration: underline;
  }

  /* Price Section */
  .price-section {
    margin-bottom: 30px;
  }

  .price-main {
    font-size: 2rem;
    font-weight: 700;
    color: #ff6b6b;
    margin-bottom: 5px;
  }

  .price-compare {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .original-price {
    color: #999;
    text-decoration: line-through;
    font-size: 16px;
  }

  .discount-badge {
    background: #ff6b6b;
    color: white;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
  }

  /* Stock Status */
  .stock-status {
    margin-bottom: 30px;
  }

  .stock-available,
  .stock-warning,
  .stock-out {
    padding: 12px 16px;
    border-radius: 8px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .stock-available {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
  }

  .stock-warning {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
  }

  .stock-out {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
  }

  /* Product Actions */
  .product-actions {
    margin-bottom: 40px;
  }

  /* Quantity Selector */
  .quantity-selector {
    margin-bottom: 20px;
  }

  .quantity-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 10px;
    display: block;
  }

  .quantity-controls {
    display: flex;
    align-items: center;
    gap: 0;
    max-width: 150px;
    margin-bottom: 5px;
  }

  .quantity-btn {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    color: #495057;
    padding: 10px 15px;
    font-weight: 600;
    transition: all 0.3s ease;
    cursor: pointer;
    border-radius: 0;
  }

  .quantity-minus {
    border-radius: 8px 0 0 8px;
  }

  .quantity-plus {
    border-radius: 0 8px 8px 0;
  }

  .quantity-btn:hover {
    background: #e9ecef;
    color: #333;
  }

  .quantity-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }

  .quantity-input {
    border: 1px solid #e9ecef;
    border-left: none;
    border-right: none;
    padding: 10px 15px;
    text-align: center;
    font-weight: 600;
    width: 80px;
    outline: none;
  }

  .quantity-input:focus {
    border-color: #ff6b6b;
    box-shadow: 0 0 0 2px rgba(255, 107, 107, 0.1);
  }

  .quantity-note {
    color: #6c757d;
    font-size: 14px;
  }

  .action-buttons {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
  }

  .btn {
    border: none;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
  }

  .btn-add-cart {
    background: #ff6b6b;
    color: white;
    padding: 15px 30px;
    font-size: 16px;
  }

  .btn-add-cart:hover:not(:disabled) {
    background: #ff5252;
    transform: translateY(-2px);
  }

  .btn-add-cart:disabled {
    opacity: 0.6;
    cursor: not-allowed;
  }

  .btn-wishlist {
    background: white;
    border: 2px solid #e9ecef;
    color: #6c757d;
    padding: 15px;
    width: 50px;
    height: 50px;
    justify-content: center;
  }

  .btn-wishlist:hover {
    border-color: #ff6b6b;
    color: #ff6b6b;
    transform: translateY(-2px);
  }

  .btn-back {
    background: #f8f9fa;
    color: #6c757d;
    padding: 12px 20px;
    border: 1px solid #e9ecef;
  }

  .btn-back:hover {
    background: #e9ecef;
    color: #495057;
  }

  /* Product Details */
  .product-details {
    border-top: 1px solid #e9ecef;
    padding-top: 40px;
  }

  .product-details h3 {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 30px;
    color: #333;
  }

  .detail-section {
    margin-bottom: 30px;
  }

  .detail-section h4 {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 15px;
    color: #495057;
  }

  .detail-section p {
    color: #6c757d;
    line-height: 1.6;
  }

  .spec-list {
    display: grid;
    gap: 10px;
  }

  .spec-item {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #f8f9fa;
  }

  .spec-label {
    font-weight: 500;
    color: #495057;
  }

  .spec-value {
    color: #6c757d;
  }

  /* Related Products */
  .related-products {
    margin-top: 60px;
    padding-top: 40px;
    border-top: 1px solid #e9ecef;
  }

  .related-products h3 {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 30px;
    text-align: center;
    color: #333;
  }

  .related-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
  }

  .related-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
  }

  .related-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
  }

  .related-link {
    text-decoration: none;
    color: inherit;
    display: block;
  }

  .related-image {
    height: 200px;
    overflow: hidden;
  }

  .related-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
  }

  .related-card:hover .related-image img {
    transform: scale(1.05);
  }

  .related-info {
    padding: 20px;
  }

  .related-info h4 {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 10px;
    color: #333;
  }

  .related-price {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .current-price {
    font-size: 1.1rem;
    font-weight: 700;
    color: #ff6b6b;
  }

  /* Responsive Design */
  @media (max-width: 992px) {
    .product-info {
      padding-left: 0;
      margin-top: 30px;
    }

    .product-title {
      font-size: 2rem;
    }

    .main-product-image {
      height: 400px;
    }
  }

  @media (max-width: 768px) {
    .product-detail-section {
      padding: 40px 0;
    }

    .product-title {
      font-size: 1.8rem;
    }

    .price-main {
      font-size: 1.5rem;
    }

    .action-buttons {
      flex-direction: column;
    }

    .btn-add-cart {
      width: 100%;
      justify-content: center;
    }

    .main-product-image {
      height: 300px;
    }

    .thumbnail-img {
      width: 60px;
      height: 60px;
    }

    .related-grid {
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
    }
  }
</style>
@endsection