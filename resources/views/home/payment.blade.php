@extends('home.layout')

@section('title', 'Payment - Order #' . $order->order_number)

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
            <a href="{{ route('cart.index') }}">Shopping Cart</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ route('checkout.index') }}">Checkout</a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">Payment</li>
        </ol>
      </nav>
    </div>
  </div>

  <!-- Payment Section -->
  <div class="payment-section">
    <div class="container">
      <div class="row">
        <!-- Left Column: Order Info & Items -->
        <div class="col-lg-7">
          <div class="payment-container">

            <!-- Order Information -->
            <div class="order-info-card">
              <h3 class="payment-title">
                <i class="fa fa-credit-card me-2"></i>
                Payment for Order #{{ $order->order_number }}
              </h3>

              <div class="order-details">
                <div class="order-summary-row">
                  <span>Order Date</span>
                  <span>{{ $order->created_at->format('d M Y, H:i') }}</span>
                </div>
                <div class="order-summary-row">
                  <span>Items Total</span>
                  <span>{{ $order->formatted_subtotal }}</span>
                </div>
                <div class="order-summary-row">
                  <span>Shipping ({{ $order->shipping_service }})</span>
                  <span>{{ $order->formatted_shipping_cost }}</span>
                </div>
                <div class="order-summary-row order-total">
                  <span><strong>Total Amount</strong></span>
                  <span><strong>{{ $order->formatted_total_amount }}</strong></span>
                </div>
              </div>

              <!-- Shipping Address -->
              <div class="shipping-info">
                <h5><i class="fa fa-map-marker-alt me-2"></i>Shipping Address</h5>
                <div class="address-display">
                  <p class="recipient-name">{{ $order->shipping_address['recipient_name'] }}</p>
                  <p class="phone">{{ $order->shipping_address['phone'] }}</p>
                  <p class="address">{{ $order->shipping_address['address'] }}</p>
                  <p class="location">
                    {{ $order->shipping_address['village_name'] }},
                    {{ $order->shipping_address['district_name'] }},
                    {{ $order->shipping_address['city_name'] }},
                    {{ $order->shipping_address['province_name'] }}
                    {{ $order->shipping_address['postal_code'] }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Order Items -->
            <div class="order-items-card">
              <h4 class="payment-section-title">
                <i class="fa fa-box me-2"></i>
                Order Items ({{ $order->items->count() }} items)
              </h4>

              <div class="items-list">
                @foreach($order->items as $item)
                  <div class="order-item">
                    <div class="item-image">
                      @if($item->product_image)
                        <img src="{{ asset('storage/' . $item->product_image) }}"
                             alt="{{ $item->product_name }}"
                             onerror="this.src='{{ asset('furni-1.0.0/images/product-1.png') }}';">
                      @else
                        <img src="{{ asset('furni-1.0.0/images/product-1.png') }}"
                             alt="{{ $item->product_name }}">
                      @endif
                    </div>
                    <div class="item-details">
                      <h6 class="item-name">{{ $item->product_name }}</h6>
                      @if($item->product_sku)
                        <p class="item-sku">SKU: {{ $item->product_sku }}</p>
                      @endif
                      <div class="item-price-info">
                        <span class="item-price">{{ $item->formatted_product_price }}</span>
                        <span class="item-quantity">x {{ $item->quantity }}</span>
                        <span class="item-subtotal">= {{ $item->formatted_subtotal }}</span>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>

          </div>
        </div>

        <!-- Right Column: Payment Methods -->
        <div class="col-lg-5">
          <div class="payment-methods-card">
            <h4 class="payment-section-title">
              <i class="fa fa-wallet me-2"></i>
              Choose Payment Method
            </h4>

            @if($paymentTransaction && $paymentTransaction->midtrans_response && isset($paymentTransaction->midtrans_response['token']))
              <!-- Existing Payment Token -->
              <div class="payment-link-section">
                <div class="alert alert-info">
                  <i class="fa fa-info-circle me-2"></i>
                  Your payment is ready. Click the button below to complete your payment securely with Midtrans.
                </div>

                <div class="payment-status mb-3">
                  <span class="status-label">Payment Status:</span>
                  {!! $paymentTransaction->status_badge !!}
                </div>

                <button id="paymentButton" class="btn btn-primary btn-payment" data-token="{{ $paymentTransaction->midtrans_response['token'] }}">
                  <i class="fa fa-credit-card me-2"></i>
                  Pay Now - {{ $order->formatted_total_amount }}
                </button>
              </div>
            @else
              <!-- Create New Payment -->
              <div class="payment-creation">
                <div class="alert alert-warning">
                  <i class="fa fa-exclamation-triangle me-2"></i>
                  Payment token is being generated. Please wait a moment...
                </div>
                <div class="text-center">
                  <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Creating payment...</span>
                  </div>
                </div>
              </div>
            @endif

            <!-- Payment Instructions -->
            <div class="payment-instructions">
              <h6><i class="fa fa-lightbulb me-2"></i>Payment Instructions:</h6>
              <ul>
                <li>Click "Pay Now" button to open Midtrans payment page</li>
                <li>Choose your preferred payment method (Credit Card, Bank Transfer, E-Wallet, etc.)</li>
                <li>Complete the payment according to the selected method</li>
                <li>You will be redirected back after payment completion</li>
                <li>Order status will be updated automatically</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('scripts')
<!-- Midtrans Snap JS -->
@if(app(App\Services\MidtransService::class)->getEnvironment() === 'production')
    <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ app(App\Services\MidtransService::class)->getClientKey() }}"></script>
@else
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ app(App\Services\MidtransService::class)->getClientKey() }}"></script>
@endif

<script>
$(document).ready(function() {
    // Check if payment button exists
    const paymentButton = document.getElementById('paymentButton');

    if (paymentButton) {
        paymentButton.addEventListener('click', function() {
            const token = paymentButton.getAttribute('data-token');

            if (token) {
                // Use Snap to open payment
                window.snap.pay(token, {
                    onSuccess: function(result) {
                        console.log('Payment success:', result);
                        Notiflix.Notify.success('Payment completed successfully!');

                        // Simulate Midtrans webhook notification for demo
                        simulateWebhookNotification(result);
                    },
                    onPending: function(result) {
                        console.log('Payment pending:', result);
                        Notiflix.Notify.info('Payment is being processed. You will receive confirmation shortly.');

                        // Redirect to pending page
                        setTimeout(function() {
                            window.location.href = '{{ route("checkout.pending", ["order_id" => $order->order_number]) }}';
                        }, 2000);
                    },
                    onError: function(result) {
                        console.log('Payment error:', result);
                        Notiflix.Notify.failure('Payment failed. Please try again.');

                        // Redirect to error page
                        setTimeout(function() {
                            window.location.href = '{{ route("checkout.error", ["order_id" => $order->order_number]) }}';
                        }, 2000);
                    },
                    onClose: function() {
                        console.log('Payment popup closed');
                        Notiflix.Notify.info('Payment cancelled. You can try again anytime.');
                    }
                });
            } else {
                // Redirect to checkout to regenerate payment
                Notiflix.Notify.warning('Payment token not available. Redirecting to checkout...');
                window.location.href = '{{ route("checkout.index") }}';
            }
        });
    } else {
        // Auto-refresh if no payment button (waiting for token generation)
        setTimeout(function() {
            window.location.reload();
        }, 5000);
    }

    // Auto-refresh payment status every 30 seconds
    let statusCheckInterval = setInterval(function() {
        checkPaymentStatus();
    }, 30000);

    function checkPaymentStatus() {
        // Simple status check by fetching the current payment page
        $.ajax({
            url: window.location.href,
            method: 'GET',
            success: function(response) {
                // Check if the response contains success indicators
                if (response.includes('settlement') || response.includes('paid') || response.includes('success')) {
                    clearInterval(statusCheckInterval);
                    Notiflix.Notify.success('Payment completed! Redirecting...');
                    setTimeout(function() {
                        window.location.href = '{{ route("checkout.success", ["order_id" => $order->order_number]) }}';
                    }, 2000);
                } else if (response.includes('cancel') || response.includes('expire') || response.includes('failure')) {
                    clearInterval(statusCheckInterval);
                    Notiflix.Notify.warning('Payment was cancelled or expired.');
                }
            },
            error: function() {
                // Continue checking on error (maybe connection issue)
            }
        });
    }

    // Function to simulate Midtrans webhook notification for demo
    function simulateWebhookNotification(paymentResult) {
        console.log('Simulating Midtrans webhook notification...', paymentResult);

        // Create notification payload similar to real Midtrans webhook
        const notification = {
            order_id: '{{ $order->order_number }}',
            transaction_status: 'settlement',
            payment_type: paymentResult.payment_type || 'snap',
            transaction_id: paymentResult.transaction_id || '{{ $order->order_number }}',
            fraud_status: 'accept',
            gross_amount: paymentResult.gross_amount || '{{ $order->total_amount }}',
            currency: 'IDR',
            transaction_time: new Date().toISOString(),
            settlement_time: new Date().toISOString(),
            signature_key: 'demo_signature',
            // Frontend simulation marker
            _frontend_simulation: true
        };

        $.ajax({
            url: '{{ route("payment.notification") }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: notification,
            success: function(response) {
                console.log('Webhook simulation response:', response);

                if (response.status === 'ok') {
                    Notiflix.Notify.success('Payment processed! Order status updated.');

                    // Short delay then redirect
                    setTimeout(function() {
                        window.location.href = '{{ route("checkout.success", ["order_id" => $order->order_number]) }}';
                    }, 1500);
                } else {
                    console.error('Webhook simulation failed:', response);
                    // Payment was successful, redirect anyway
                    Notiflix.Notify.warning('Payment successful! Redirecting to order details...');
                    setTimeout(function() {
                        window.location.href = '{{ route("checkout.success", ["order_id" => $order->order_number]) }}';
                    }, 1500);
                }
            },
            error: function(xhr, status, error) {
                console.error('Webhook simulation request failed:', {
                    status: status,
                    error: error,
                    response: xhr.responseJSON
                });

                // Payment was successful, redirect anyway
                Notiflix.Notify.warning('Payment successful! Redirecting to order details...');
                setTimeout(function() {
                    window.location.href = '{{ route("checkout.success", ["order_id" => $order->order_number]) }}';
                }, 1500);
            }
        });
    }
});
</script>
@endsection

@section('styles')
<style>
.payment-section {
    padding: 40px 0;
    background-color: white;
}

.payment-container {
    max-width: 100%;
}

.order-info-card,
.payment-methods-card,
.order-items-card {
    background: #fafafa;
    border-radius: 4px;
    padding: 15px;
    margin-bottom: 15px;
    border: 1px solid #e9ecef;
}

.payment-title {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
}

.payment-section-title {
    font-size: 14px;
    font-weight: 600;
    color: #333;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
}

.payment-title i,
.payment-section-title i {
    color: #007bff;
    font-size: 14px;
}

.order-summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #eee;
    font-size: 12px;
}

.order-summary-row:last-child {
    border-bottom: none;
}

.order-total {
    font-size: 13px;
    margin-top: 8px;
    padding-top: 8px;
    border-top: 1px solid #ddd;
}

.shipping-info {
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid #eee;
}

.shipping-info h5 {
    font-size: 13px;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
}

.address-display {
    background: #f5f5f5;
    padding: 10px;
    border-radius: 4px;
    border-left: 3px solid #007bff;
}

.address-display p {
    margin-bottom: 3px;
    font-size: 11px;
    line-height: 1.3;
}

.address-display .recipient-name {
    font-weight: 600;
    color: #333;
    font-size: 12px;
}

.address-display .phone {
    color: #666;
}

.payment-link-section {
    text-align: center;
}

.payment-status {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-bottom: 10px;
}

.status-label {
    font-weight: 500;
    color: #333;
    font-size: 12px;
}

.btn-payment {
    padding: 10px 16px;
    font-size: 12px;
    font-weight: 600;
    border-radius: 4px;
    text-transform: none;
    letter-spacing: normal;
    min-width: 180px;
}

.payment-instructions {
    margin-top: 15px;
    padding: 12px;
    background: #f5f5f5;
    border-radius: 4px;
    border-left: 3px solid #17a2b8;
}

.payment-instructions h6 {
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
    font-size: 12px;
}

.payment-instructions ul {
    margin-bottom: 0;
    padding-left: 16px;
}

.payment-instructions li {
    margin-bottom: 4px;
    font-size: 11px;
    color: #555;
    line-height: 1.3;
}

.order-item {
    display: flex;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #eee;
}

.order-item:last-child {
    border-bottom: none;
}

.item-image {
    width: 50px;
    height: 50px;
    border-radius: 4px;
    overflow: hidden;
    margin-right: 10px;
    flex-shrink: 0;
}

.item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.item-details {
    flex: 1;
}

.item-name {
    font-size: 12px;
    font-weight: 600;
    color: #333;
    margin-bottom: 2px;
    line-height: 1.2;
}

.item-sku {
    font-size: 10px;
    color: #666;
    margin-bottom: 4px;
}

.item-price-info {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 11px;
}

.item-price {
    color: #28a745;
    font-weight: 600;
}

.item-quantity {
    color: #666;
}

.item-subtotal {
    color: #333;
    font-weight: 600;
    margin-left: auto;
}

.alert {
    padding: 10px 12px;
    font-size: 11px;
    border-radius: 4px;
}

.alert i {
    font-size: 12px;
}

.spinner-border {
    width: 20px;
    height: 20px;
}

.payment-methods-card {
    position: sticky;
    top: 20px;
    max-height: calc(100vh - 40px);
    overflow-y: auto;
}

.items-list {
    max-height: 400px;
    overflow-y: auto;
}

@media (max-width: 768px) {
    .payment-methods-card {
        position: static;
        max-height: none;
        margin-top: 15px;
    }

    .items-list {
        max-height: none;
        overflow-y: visible;
    }
    .payment-section {
        padding: 40px 0;
    }

    .order-info-card,
    .payment-methods-card,
    .order-items-card {
        padding: 1.5rem;
    }

    .payment-title {
        font-size: 1.25rem;
    }

    .order-summary-row {
        font-size: 0.9rem;
    }

    .btn-payment {
        width: 100%;
        min-width: auto;
    }

    .order-item {
        flex-direction: column;
        align-items: flex-start;
        text-align: left;
    }

    .item-image {
        margin-right: 0;
        margin-bottom: 1rem;
    }

    .item-price-info {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
    }

    .item-subtotal {
        margin-left: 0;
    }
}
</style>
@endsection