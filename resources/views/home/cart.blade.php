@extends('home.layout')

@section('title', 'Shopping Cart - Marketplace')

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
          <li class="breadcrumb-item active" aria-current="page">Shopping Cart</li>
        </ol>
      </nav>
    </div>
  </div>

  <!-- Cart Section -->
  <div class="cart-section">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <h2 class="section-title">Shopping Cart</h2>
        </div>
      </div>

      @if($cartItems->count() > 0)
        <div class="row">
          <!-- Cart Items -->
          <div class="col-lg-9">
            <div class="cart-items-container">
              @foreach($cartItems as $item)
                <div class="cart-item" data-cart-id="{{ $item->id }}">
                  <div class="cart-item-image">
                    @if($item->product->images->isNotEmpty())
                      <img src="{{ asset($item->product->images->first()->url) }}"
                           alt="{{ $item->product->name }}"
                           onerror="this.src='{{ asset('furni-1.0.0/images/product-1.png') }}';">
                    @else
                      <img src="{{ asset('furni-1.0.0/images/product-1.png') }}"
                           alt="{{ $item->product->name }}">
                    @endif
                  </div>

                  <div class="cart-item-details">
                    <h6 class="cart-item-name">
                      <a href="{{ route('product.detail', $item->product->id) }}">
                        {{ $item->product->name }}
                      </a>
                    </h6>
                    <small class="cart-item-sku">{{ $item->product->sku }}</small>
                    <p class="cart-item-price">Rp{{ number_format($item->product->price, 0, ',', '.') }}</p>
                  </div>

                  <div class="cart-item-quantity">
                    <div class="quantity-controls">
                      <button type="button" class="quantity-btn" onclick="updateCartQuantity({{ $item->id }}, -1)">-</button>
                      <input type="number"
                             class="quantity-input"
                             value="{{ $item->quantity }}"
                             min="1"
                             max="{{ $item->product->stock_quantity }}"
                             onchange="updateCartQuantity({{ $item->id }}, 0, this.value)">
                      <button type="button" class="quantity-btn" onclick="updateCartQuantity({{ $item->id }}, 1)">+</button>
                    </div>
                  </div>

                  <div class="cart-item-subtotal">
                    <span class="subtotal-amount" id="subtotal-{{ $item->id }}">
                      Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                    </span>
                  </div>

                  <div class="cart-item-actions">
                    <button type="button" class="btn-remove" onclick="removeCartItem({{ $item->id }})">
                      <i class="fas fa-times"></i>
                    </button>
                  </div>
                </div>
              @endforeach
            </div>

            <div class="cart-actions">
              <button type="button" class="btn btn-sm" onclick="clearCart()">
                <i class="fas fa-trash"></i> Clear All
              </button>
              <a href="{{ route('shop') }}" class="btn btn-sm btn-outline">
                Continue Shopping
              </a>
            </div>

            <!-- Order Summary -->
            <div class="cart-summary-inline">
              <div class="summary-row">
                <span>Subtotal ({{ $cartItems->sum('quantity') }} items)</span>
                <strong id="cart-total">Rp{{ number_format($total, 0, ',', '.') }}</strong>
              </div>
              <div class="checkout-actions-inline">
                @auth
                  <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-sm">
                    Checkout
                  </a>
                @else
                  <a href="{{ route('login') }}" class="btn btn-primary btn-sm">
                    Login to Checkout
                  </a>
                @endauth
              </div>
            </div>
          </div>

          <!-- Product Recommendations -->
          <div class="col-lg-3">
            <div class="recommendations">
              <h6>Similar Products</h6>
              <div class="recommendations-list">
                @php
                  // Get categories from cart items for recommendations
                  $categoryIds = $cartItems->pluck('product.category_id')->unique();
                  $recommendedProducts = App\Models\Product::whereIn('category_id', $categoryIds)
                    ->whereNotIn('id', $cartItems->pluck('product_id'))
                    ->where('is_active', true)
                    ->where('stock_quantity', '>', 0)
                    ->take(6)
                    ->get();
                @endphp

                @forelse($recommendedProducts as $product)
                  <div class="recommendation-item">
                    <div class="rec-image">
                      @if($product->images->isNotEmpty())
                        <img src="{{ asset($product->images->first()->url) }}"
                             alt="{{ $product->name }}"
                             onerror="this.src='{{ asset('furni-1.0.0/images/product-1.png') }}';">
                      @else
                        <img src="{{ asset('furni-1.0.0/images/product-1.png') }}"
                             alt="{{ $product->name }}">
                      @endif
                    </div>
                    <div class="rec-details">
                      <small class="rec-name">
                        <a href="{{ route('product.detail', $product->id) }}">
                          {{ Str::limit($product->name, 40) }}
                        </a>
                      </small>
                      <div class="rec-price">Rp{{ number_format($product->price, 0, ',', '.') }}</div>
                      <button class="btn btn-xs btn-add" onclick="addToCart({{ $product->id }})">
                        Add
                      </button>
                    </div>
                  </div>
                @empty
                  <p class="no-recommendations">No similar products found</p>
                @endforelse
              </div>
            </div>
          </div>
        </div>

      @else
        <!-- Empty Cart -->
        <div class="row">
          <div class="col-12">
            <div class="empty-cart">
              <div class="empty-cart-icon">
                <i class="fas fa-shopping-cart fa-4x"></i>
              </div>
              <h3>Your cart is empty</h3>
              <p>Looks like you haven't added any items to your cart yet.</p>
              <a href="{{ route('shop') }}" class="btn btn-primary">
                <i class="fas fa-shopping-bag me-2"></i>Start Shopping
              </a>
            </div>
          </div>
        </div>
      @endif
    </div>
  </div>

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

  /* Cart Section */
  .cart-section {
    padding: 40px 0;
    background: white;
  }

  .section-title {
    font-size: 1.8rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 30px;
    text-align: left;
  }

  /* Cart Items */
  .cart-items-container {
    background: #fafafa;
    border-radius: 4px;
    border: 1px solid #e9ecef;
    overflow: hidden;
    margin-bottom: 20px;
  }

  .cart-item {
    display: flex;
    align-items: center;
    padding: 15px;
    border-bottom: 1px solid #eee;
    gap: 15px;
  }

  .cart-item:last-child {
    border-bottom: none;
  }

  .cart-item-image {
    flex-shrink: 0;
    width: 60px;
    height: 60px;
    border-radius: 4px;
    overflow: hidden;
  }

  .cart-item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .cart-item-details {
    flex: 1;
    min-width: 0;
  }

  .cart-item-name {
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 4px;
  }

  .cart-item-name a {
    color: #333;
    text-decoration: none;
  }

  .cart-item-name a:hover {
    color: #007bff;
  }

  .cart-item-sku {
    color: #888;
    font-size: 11px;
    margin-bottom: 4px;
  }

  .cart-item-price {
    color: #333;
    font-weight: 500;
    font-size: 13px;
    margin: 0;
  }

  /* Quantity Controls */
  .cart-item-quantity {
    flex-shrink: 0;
    text-align: center;
  }

  .quantity-controls {
    display: flex;
    align-items: center;
    gap: 0;
  }

  .quantity-btn {
    background: #fff;
    border: 1px solid #ddd;
    color: #666;
    padding: 4px 8px;
    font-weight: 500;
    transition: all 0.2s ease;
    cursor: pointer;
    border-radius: 2px;
    font-size: 12px;
  }

  .quantity-btn:hover {
    background: #f5f5f5;
    color: #333;
  }

  .quantity-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }

  .quantity-input {
    border: 1px solid #ddd;
    border-left: none;
    border-right: none;
    padding: 4px 8px;
    text-align: center;
    font-weight: 500;
    width: 45px;
    outline: none;
    font-size: 12px;
  }

  .quantity-input:focus {
    border-color: #007bff;
  }

  /* Subtotal */
  .cart-item-subtotal {
    flex-shrink: 0;
    text-align: right;
    font-weight: 600;
    font-size: 14px;
    color: #333;
    min-width: 80px;
  }

  /* Actions */
  .cart-item-actions {
    flex-shrink: 0;
  }

  .btn-remove {
    background: none;
    border: none;
    color: #dc3545;
    padding: 4px;
    cursor: pointer;
    transition: all 0.2s ease;
    border-radius: 2px;
  }

  .btn-remove:hover {
    background: #f8f9fa;
    color: #c82333;
  }

  /* Cart Actions */
  .cart-actions {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
  }

  .btn {
    border: 1px solid #ddd;
    background: #fff;
    color: #333;
    padding: 6px 12px;
    font-size: 12px;
    font-weight: 500;
    border-radius: 2px;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .btn:hover {
    background: #f5f5f5;
    border-color: #bbb;
  }

  .btn-primary {
    background: #007bff;
    color: white;
    border-color: #007bff;
  }

  .btn-primary:hover {
    background: #0056b3;
    border-color: #0056b3;
  }

  .btn-outline {
    background: transparent;
    color: #007bff;
    border-color: #007bff;
  }

  .btn-outline:hover {
    background: #007bff;
    color: white;
  }

  .btn-sm {
    padding: 4px 8px;
    font-size: 11px;
  }

  .btn-xs {
    padding: 2px 6px;
    font-size: 10px;
  }

  /* Inline Summary */
  .cart-summary-inline {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 4px;
    padding: 15px;
    margin-bottom: 20px;
  }

  .summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
    font-size: 14px;
  }

  .checkout-actions-inline {
    text-align: right;
  }

  /* Recommendations */
  .recommendations {
    background: #fafafa;
    border: 1px solid #e9ecef;
    border-radius: 4px;
    padding: 15px;
    position: sticky;
    top: 20px;
  }

  .recommendations h6 {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 15px;
    color: #333;
  }

  .recommendations-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  .recommendation-item {
    display: flex;
    gap: 10px;
    padding: 8px;
    background: white;
    border: 1px solid #eee;
    border-radius: 4px;
  }

  .rec-image {
    flex-shrink: 0;
    width: 40px;
    height: 40px;
    border-radius: 3px;
    overflow: hidden;
  }

  .rec-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .rec-details {
    flex: 1;
    min-width: 0;
  }

  .rec-name {
    font-size: 11px;
    line-height: 1.3;
    margin-bottom: 4px;
    display: block;
  }

  .rec-name a {
    color: #333;
    text-decoration: none;
  }

  .rec-name a:hover {
    color: #007bff;
  }

  .rec-price {
    font-size: 10px;
    font-weight: 600;
    color: #333;
    margin-bottom: 4px;
  }

  .btn-add {
    background: #28a745;
    color: white;
    border: 1px solid #28a745;
  }

  .btn-add:hover {
    background: #218838;
    border-color: #218838;
  }

  .no-recommendations {
    font-size: 11px;
    color: #888;
    text-align: center;
    margin: 0;
    padding: 20px;
  }

  /* Empty Cart */
  .empty-cart {
    text-align: center;
    padding: 80px 20px;
  }

  .empty-cart-icon {
    color: #e9ecef;
    margin-bottom: 30px;
  }

  .empty-cart h3 {
    font-size: 1.8rem;
    font-weight: 600;
    color: #495057;
    margin-bottom: 15px;
  }

  .empty-cart p {
    color: #6c757d;
    font-size: 16px;
    margin-bottom: 30px;
  }

  /* Hide mobile delete button on desktop */
  .mobile-delete-btn {
    display: none;
  }

  /* Responsive Design */
  @media (max-width: 768px) {
    .cart-section {
      padding: 20px 0;
    }

    .section-title {
      font-size: 1.4rem;
      margin-bottom: 15px;
    }

    .cart-items-container {
      margin-bottom: 10px;
      padding: 5px;
      border-radius: 2px;
    }

    .cart-item {
      flex-direction: row;
      align-items: center;
      gap: 8px;
      padding: 8px;
      border-radius: 4px;
      margin-bottom: 5px;
      background: #f8f9fa;
      border-bottom: 1px solid #eee;
    }

    .cart-item-image {
      width: 45px;
      height: 45px;
      border-radius: 3px;
    }

    .cart-item-details {
      flex: 1;
      min-width: 0;
    }

    .cart-item-name {
      font-size: 10px;
      font-weight: 500;
      margin-bottom: 1px;
      line-height: 1.1;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .cart-item-sku {
      display: none;
    }

    .cart-item-price {
      font-size: 9px;
      margin: 0;
      color: #333;
      font-weight: 500;
    }

    .cart-item-quantity {
      flex-shrink: 0;
      margin-right: 5px;
    }

    .quantity-label {
      display: none;
    }

    .quantity-controls {
      scale: 0.7;
      margin-bottom: 0;
    }

    .quantity-btn {
      padding: 3px 5px;
      font-size: 10px;
    }

    .quantity-input {
      width: 35px;
      padding: 3px 5px;
      font-size: 10px;
    }

    .stock-note {
      display: none;
    }

    .cart-item-subtotal {
      flex-shrink: 0;
      font-size: 9px;
      font-weight: 600;
      min-width: auto;
      text-align: right;
    }

    .cart-item-actions {
      display: none;
    }

    .mobile-delete-btn {
      display: block;
      text-align: center;
      padding: 3px 8px 0;
      border-top: 1px solid #eee;
      background: #f8f9fa;
      margin: 0 -8px -8px;
    }

    .btn-remove-mobile {
      background: none;
      border: none;
      color: #dc3545;
      font-size: 8px;
      padding: 2px 4px;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 2px;
      margin: 0 auto;
      transition: color 0.3s ease;
    }

    .btn-remove-mobile:hover {
      color: #c82333;
    }

    .btn-remove-mobile i {
      font-size: 8px;
    }

    .cart-actions {
      flex-direction: row;
      justify-content: space-between;
      align-items: center;
      gap: 5px;
      margin-bottom: 10px;
    }

    .cart-actions .btn {
      flex: none;
      padding: 5px 8px;
      font-size: 9px;
      border-radius: 3px;
      display: flex;
      align-items: center;
      justify-content: center;
      min-width: 30px;
    }

    .cart-actions .btn i {
      font-size: 9px;
    }

    .cart-actions .btn-outline-primary {
      background: #f8f9fa;
      border: 1px solid #ddd;
      color: #495057;
    }

    .cart-actions .btn-outline-danger {
      background: #f8d7da;
      border: 1px solid #f5c6cb;
      color: #721c24;
    }

    .cart-actions .btn .btn-text {
      display: none;
    }

    .cart-summary-inline {
      padding: 8px;
      margin-bottom: 10px;
    }

    .summary-row {
      font-size: 10px;
      margin-bottom: 5px;
    }

    .checkout-actions-inline .btn {
      padding: 5px 8px;
      font-size: 10px;
    }

    .recommendations {
      padding: 8px;
      margin-top: 10px;
    }

    .recommendations h6 {
      font-size: 11px;
      margin-bottom: 8px;
    }

    .recommendations-list {
      gap: 5px;
    }

    .recommendation-item {
      padding: 5px;
      gap: 5px;
    }

    .rec-image {
      width: 30px;
      height: 30px;
    }

    .rec-name {
      font-size: 9px;
      margin-bottom: 2px;
    }

    .rec-price {
      font-size: 8px;
      margin-bottom: 2px;
    }

    .btn-xs {
      padding: 1px 3px;
      font-size: 8px;
    }

    .no-recommendations {
      font-size: 9px;
      padding: 10px;
    }

    /* Stack layout on mobile */
    .row .col-lg-9,
    .row .col-lg-3 {
      flex: 0 0 100%;
      max-width: 100%;
    }

    .recommendations {
      order: 2;
    }
  }
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // CSRF Token setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
});

// Update cart quantity
function updateCartQuantity(cartId, change, newValue = null) {
    const cartItem = document.querySelector(`[data-cart-id="${cartId}"]`);
    const quantityInput = cartItem.querySelector('.quantity-input');

    let quantity;
    if (newValue !== null) {
        quantity = parseInt(newValue);
    } else {
        const currentQuantity = parseInt(quantityInput.value);
        quantity = currentQuantity + change;
    }

    // Validate quantity
    const min = parseInt(quantityInput.getAttribute('min'));
    const max = parseInt(quantityInput.getAttribute('max'));

    if (quantity < min) {
        quantity = min;
    } else if (quantity > max) {
        quantity = max;
        showNotification('warning', `Maximum ${max} items available`);
    }

    quantityInput.value = quantity;

    // Send AJAX request
    $.ajax({
        url: '{{ route("cart.update") }}',
        method: 'PUT',
        data: {
            cart_id: cartId,
            quantity: quantity
        },
        success: function(response) {
            if (response.success) {
                // Update subtotal
                const subtotalElement = document.getElementById(`subtotal-${cartId}`);
                subtotalElement.textContent = 'Rp' + new Intl.NumberFormat('id-ID').format(response.subtotal);

                // Update cart counter
                updateCartCounter(response.cart_count);

                // Recalculate totals
                recalculateTotals();

                showNotification('success', response.message);
            } else {
                showNotification('error', response.message);
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            const message = response?.message || 'Failed to update quantity';
            showNotification('error', message);

            // Revert quantity on error
            location.reload();
        }
    });
}

// Remove cart item
function removeCartItem(cartId) {
    Notiflix.Confirm.show(
        'Remove Item',
        'Are you sure you want to remove this item from your cart?',
        'Yes, Remove',
        'Cancel',
        function() {
            // Yes callback
            $.ajax({
                url: '{{ route("cart.remove") }}',
                method: 'DELETE',
                data: {
                    cart_id: cartId
                },
                success: function(response) {
                    if (response.success) {
                        // Remove the cart item from DOM
                        const cartItem = document.querySelector(`[data-cart-id="${cartId}"]`);
                        cartItem.remove();

                        // Update cart counter
                        updateCartCounter(response.cart_count);

                        // Recalculate totals or reload if cart is empty
                        if (response.cart_count === 0) {
                            location.reload();
                        } else {
                            recalculateTotals();
                        }

                        showNotification('success', response.message);
                    } else {
                        showNotification('error', response.message);
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    const message = response?.message || 'Failed to remove item';
                    showNotification('error', message);
                }
            });
        },
        function() {
            // No callback - do nothing
        }
    );
}

// Clear cart
function clearCart() {
    Notiflix.Confirm.show(
        'Clear Cart',
        'Are you sure you want to clear your entire cart?',
        'Yes, Clear All',
        'Cancel',
        function() {
            // Yes callback
            $.ajax({
                url: '{{ route("cart.clear") }}',
                method: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        showNotification('success', response.message);
                        location.reload();
                    } else {
                        showNotification('error', response.message);
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    const message = response?.message || 'Failed to clear cart';
                    showNotification('error', message);
                }
            });
        },
        function() {
            // No callback - do nothing
        }
    );
}

// Recalculate totals
function recalculateTotals() {
    let total = 0;
    let itemCount = 0;

    document.querySelectorAll('.cart-item').forEach(item => {
        const quantityInput = item.querySelector('.quantity-input');
        const quantity = parseInt(quantityInput.value);
        const subtotalText = item.querySelector('.subtotal-amount').textContent;

        // Extract number from formatted price
        const subtotal = parseInt(subtotalText.replace(/[^\d]/g, ''));
        total += subtotal;
        itemCount += quantity;
    });

    // Update totals in summary
    document.getElementById('cart-total').textContent = 'Rp' + new Intl.NumberFormat('id-ID').format(total);
    document.getElementById('final-total').textContent = 'Rp' + new Intl.NumberFormat('id-ID').format(total);

    // Update item count
    const summaryLine = document.querySelector('.summary-line span');
    if (summaryLine) {
        summaryLine.textContent = `Subtotal (${itemCount} items):`;
    }
}

// Add to cart function for recommendations
function addToCart(productId) {
    $.ajax({
        url: '{{ route("cart.add") }}',
        method: 'POST',
        data: {
            product_id: productId,
            quantity: 1
        },
        success: function(response) {
            if (response.success) {
                showNotification('success', response.message);
                updateCartCounter(response.cart_count);
                location.reload();
            } else {
                showNotification('error', response.message);
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            const message = response?.message || 'Failed to add item to cart';
            showNotification('error', message);
        }
    });
}

// Show notification
function showNotification(type, message) {
    if (typeof Notiflix !== 'undefined') {
        switch(type) {
            case 'success':
                Notiflix.Notify.success(message);
                break;
            case 'warning':
                Notiflix.Notify.warning(message);
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