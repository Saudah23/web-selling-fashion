@extends('home.layout')

@section('title', 'Payment Success - Marketplace')

@section('content')

  <!-- Success Section -->
  <div class="success-section">
    <div class="container">
      <div class="row">
        <!-- Left Column: Order Details -->
        <div class="col-lg-8">
          <div class="success-container">

            <!-- Success Icon & Message -->
            <div class="success-header">
              <div class="success-icon">
                <i class="fas fa-check-circle"></i>
              </div>
              <div class="success-message">
                <h2 class="success-title">Payment Successful!</h2>
                <p class="success-subtitle">
                  Thank you for your order. Your payment has been processed successfully.
                </p>
              </div>
            </div>

            @if($order)
              <!-- Order Details -->
              <div class="order-details-card">
                <h4>Order Details</h4>
                <div class="detail-row">
                  <span class="detail-label">Order Number:</span>
                  <span class="detail-value">#{{ $order->order_number }}</span>
                </div>
                <div class="detail-row">
                  <span class="detail-label">Order Date:</span>
                  <span class="detail-value">{{ $order->created_at->format('d M Y, H:i') }}</span>
                </div>
                <div class="detail-row">
                  <span class="detail-label">Total Amount:</span>
                  <span class="detail-value total-amount">{{ $order->formatted_total_amount }}</span>
                </div>
                <div class="detail-row">
                  <span class="detail-label">Payment Status:</span>
                  <span class="detail-value">
                    <span class="badge bg-success">Paid</span>
                  </span>
                </div>
                <div class="detail-row">
                  <span class="detail-label">Shipping Address:</span>
                  <span class="detail-value">
                    {{ $order->shipping_address['recipient_name'] }}<br>
                    {{ $order->shipping_address['phone'] }}<br>
                    {{ $order->shipping_address['address'] }}<br>
                    {{ $order->shipping_address['city_name'] }},
                    {{ $order->shipping_address['province_name'] }}
                  </span>
                </div>
              </div>

              <!-- Next Steps -->
              <div class="next-steps">
                <h5><i class="fas fa-info-circle me-2"></i>What's Next?</h5>
                <ul>
                  <li>You will receive an order confirmation email shortly</li>
                  <li>We will process your order within 1-2 business days</li>
                  <li>Your order will be shipped via {{ $order->shipping_service }}</li>
                  <li>You can track your order status in your account</li>
                </ul>
              </div>

              <!-- Action Buttons -->
              <div class="action-buttons">
                @auth
                  <a href="{{ route('orders.index') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-user me-2"></i>
                    View My Orders
                  </a>
                @endauth
                <a href="{{ route('shop') }}" class="btn btn-outline-primary btn-sm">
                  <i class="fas fa-shopping-bag me-2"></i>
                  Continue Shopping
                </a>
              </div>
            @endif

          </div>
        </div>

        <!-- Right Column: Related Products -->
        <div class="col-lg-4">
          <div class="related-products">
            <h5>You Might Also Like</h5>
            <div class="products-grid">
              @php
                $relatedProducts = App\Models\Product::where('is_active', true)
                  ->where('stock_quantity', '>', 0)
                  ->inRandomOrder()
                  ->take(6)
                  ->get();
              @endphp

              @foreach($relatedProducts as $product)
                <div class="product-card">
                  <div class="product-image">
                    @if($product->images->isNotEmpty())
                      <img src="{{ asset($product->images->first()->url) }}"
                           alt="{{ $product->name }}"
                           onerror="this.src='{{ asset('furni-1.0.0/images/product-1.png') }}';">
                    @else
                      <img src="{{ asset('furni-1.0.0/images/product-1.png') }}"
                           alt="{{ $product->name }}">
                    @endif
                  </div>
                  <div class="product-info">
                    <h6 class="product-name">
                      <a href="{{ route('product.detail', $product->id) }}">
                        {{ Str::limit($product->name, 30) }}
                      </a>
                    </h6>
                    <p class="product-price">Rp{{ number_format($product->price, 0, ',', '.') }}</p>
                    <button class="btn btn-xs btn-add" onclick="addToCart({{ $product->id }})">
                      Add to Cart
                    </button>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('styles')
<style>
.success-section {
    padding: 40px 0;
    background: white;
}

.success-container {
    background: #fafafa;
    padding: 20px;
    border-radius: 4px;
    border: 1px solid #e9ecef;
}

.success-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
}

.success-icon {
    font-size: 2rem;
    color: #28a745;
}

.success-message {
    flex: 1;
}

.success-title {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
}

.success-subtitle {
    font-size: 12px;
    color: #666;
    margin: 0;
    line-height: 1.4;
}

.order-details-card {
    background: #f5f5f5;
    padding: 15px;
    border-radius: 4px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
}

.order-details-card h4 {
    font-size: 14px;
    font-weight: 600;
    color: #333;
    margin-bottom: 12px;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 8px 0;
    border-bottom: 1px solid #eee;
    font-size: 12px;
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 500;
    color: #555;
    min-width: 100px;
}

.detail-value {
    text-align: right;
    flex: 1;
    margin-left: 10px;
    line-height: 1.3;
}

.total-amount {
    font-weight: 600;
    font-size: 13px;
    color: #28a745;
}

.next-steps {
    background: #f0f8ff;
    padding: 12px;
    border-radius: 4px;
    margin-bottom: 15px;
    border-left: 3px solid #007bff;
}

.next-steps h5 {
    font-size: 12px;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
}

.next-steps ul {
    margin-bottom: 0;
    padding-left: 16px;
}

.next-steps li {
    margin-bottom: 3px;
    color: #555;
    font-size: 11px;
    line-height: 1.3;
}

.action-buttons {
    display: flex;
    gap: 8px;
    justify-content: flex-start;
    flex-wrap: wrap;
}

.action-buttons .btn {
    padding: 6px 12px;
    font-weight: 600;
    border-radius: 4px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    font-size: 11px;
}

.btn-sm {
    padding: 5px 10px;
    font-size: 11px;
}

/* Related Products */
.related-products {
    background: #fafafa;
    padding: 15px;
    border-radius: 4px;
    border: 1px solid #e9ecef;
}

.related-products h5 {
    font-size: 14px;
    font-weight: 600;
    color: #333;
    margin-bottom: 12px;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
}

.product-card {
    background: white;
    border: 1px solid #eee;
    border-radius: 4px;
    padding: 8px;
    text-align: center;
}

.product-image {
    width: 100%;
    height: 80px;
    border-radius: 3px;
    overflow: hidden;
    margin-bottom: 8px;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-info {
    text-align: center;
}

.product-name {
    font-size: 10px;
    font-weight: 500;
    margin-bottom: 4px;
    line-height: 1.2;
}

.product-name a {
    color: #333;
    text-decoration: none;
}

.product-name a:hover {
    color: #007bff;
}

.product-price {
    font-size: 9px;
    font-weight: 600;
    color: #28a745;
    margin-bottom: 6px;
}

.btn-xs {
    padding: 3px 6px;
    font-size: 9px;
    border-radius: 3px;
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

@media (max-width: 768px) {
    .success-section {
        padding: 20px 0;
    }

    .success-container {
        padding: 15px;
    }

    .success-header {
        flex-direction: column;
        text-align: center;
        gap: 10px;
    }

    .detail-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 2px;
    }

    .detail-value {
        text-align: left;
        margin-left: 0;
    }

    .action-buttons {
        flex-direction: column;
        align-items: stretch;
    }

    .products-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
    }

    .product-image {
        height: 60px;
    }

    .related-products {
        margin-top: 15px;
    }
}
</style>
@endsection

@section('scripts')
<script>
// Add to cart function
function addToCart(productId) {
    $.ajax({
        url: '{{ route("cart.add") }}',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            product_id: productId,
            quantity: 1
        },
        success: function(response) {
            if (response.success) {
                if (typeof Notiflix !== 'undefined') {
                    Notiflix.Notify.success(response.message);
                } else {
                    alert(response.message);
                }
                if (typeof updateCartCounter === 'function') {
                    updateCartCounter(response.cart_count);
                }
            } else {
                if (typeof Notiflix !== 'undefined') {
                    Notiflix.Notify.failure(response.message);
                } else {
                    alert(response.message);
                }
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            const message = response?.message || 'Failed to add item to cart';
            if (typeof Notiflix !== 'undefined') {
                Notiflix.Notify.failure(message);
            } else {
                alert(message);
            }
        }
    });
}
</script>
</style>
@endsection