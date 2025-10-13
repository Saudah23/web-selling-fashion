@extends('home.layout')

@section('title', 'Payment Pending - Marketplace')

@section('content')

  <!-- Pending Section -->
  <div class="pending-section">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
          <div class="pending-container">

            <!-- Pending Icon -->
            <div class="pending-icon">
              <i class="fas fa-clock"></i>
            </div>

            <!-- Pending Message -->
            <h1 class="pending-title">Payment Pending</h1>
            <p class="pending-subtitle">
              Your order has been created and is waiting for payment confirmation.
            </p>

            @if($order)
              <!-- Order Details -->
              <div class="order-details-card">
                <h3>Order Details</h3>
                <div class="detail-row">
                  <span class="detail-label">Order Number:</span>
                  <span class="detail-value">#{{ $order->order_number }}</span>
                </div>
                <div class="detail-row">
                  <span class="detail-label">Order Date:</span>
                  <span class="detail-value">{{ $order->created_at->format('d F Y, H:i') }}</span>
                </div>
                <div class="detail-row">
                  <span class="detail-label">Total Amount:</span>
                  <span class="detail-value total-amount">{{ $order->formatted_total_amount }}</span>
                </div>
                <div class="detail-row">
                  <span class="detail-label">Payment Status:</span>
                  <span class="detail-value">
                    <span class="badge bg-warning">Pending</span>
                  </span>
                </div>
              </div>

              <!-- Payment Instructions -->
              <div class="payment-instructions">
                <h4><i class="fas fa-exclamation-circle me-2"></i>Complete Your Payment</h4>
                <p>Your order is reserved for 24 hours. Please complete your payment to secure your order.</p>

                <div class="instruction-list">
                  <div class="instruction-item">
                    <div class="step-number">1</div>
                    <div class="step-content">
                      <strong>Check your payment method</strong>
                      <p>If you're using bank transfer, please check your mobile banking or ATM for payment instructions.</p>
                    </div>
                  </div>
                  <div class="instruction-item">
                    <div class="step-number">2</div>
                    <div class="step-content">
                      <strong>Complete the payment</strong>
                      <p>Follow the payment instructions provided by your chosen payment method.</p>
                    </div>
                  </div>
                  <div class="instruction-item">
                    <div class="step-number">3</div>
                    <div class="step-content">
                      <strong>Wait for confirmation</strong>
                      <p>Payment confirmation usually takes 1-15 minutes depending on your payment method.</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Payment Button -->
              <div class="payment-action">
                <a href="{{ route('checkout.payment', $order->order_number) }}" class="btn btn-primary btn-lg">
                  <i class="fas fa-credit-card me-2"></i>
                  Complete Payment
                </a>
              </div>
            @endif

            <!-- Status Check -->
            <div class="status-check">
              <p class="text-muted">
                <i class="fas fa-sync-alt me-2"></i>
                Checking payment status automatically...
              </p>
              <div class="progress">
                <div class="progress-bar progress-bar-striped progress-bar-animated"
                     role="progressbar" style="width: 100%"></div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
              <a href="{{ route('shop') }}" class="btn btn-outline-primary">
                <i class="fas fa-shopping-bag me-2"></i>
                Continue Shopping
              </a>
              @auth
                <a href="{{ route('customer.dashboard') }}" class="btn btn-outline-secondary">
                  <i class="fas fa-user me-2"></i>
                  My Account
                </a>
              @endauth
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    @if($order)
        // Auto-refresh to check payment status every 15 seconds
        let statusCheckInterval = setInterval(function() {
            checkPaymentStatus();
        }, 15000);

        // Also check once immediately after 5 seconds
        setTimeout(function() {
            checkPaymentStatus();
        }, 5000);

        function checkPaymentStatus() {
            $.get('{{ route("checkout.payment", $order->order_number) }}')
                .done(function(response) {
                    // Check if payment is completed
                    if (response.includes('settlement') || response.includes('success')) {
                        clearInterval(statusCheckInterval);
                        Notiflix.Notify.success('Payment confirmed! Redirecting...');
                        setTimeout(function() {
                            window.location.href = '{{ route("checkout.success", ["order_id" => $order->order_number]) }}';
                        }, 2000);
                    }
                })
                .fail(function() {
                    // Stop checking on error
                    console.log('Status check failed');
                });
        }

        // Stop checking after 30 minutes
        setTimeout(function() {
            clearInterval(statusCheckInterval);
        }, 30 * 60 * 1000);
    @endif
});
</script>
@endsection

@section('styles')
<style>
.pending-section {
    padding: 80px 0;
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    min-height: 80vh;
    display: flex;
    align-items: center;
}

.pending-container {
    text-align: center;
    background: white;
    padding: 3rem;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

.pending-icon {
    font-size: 4rem;
    color: #f39c12;
    margin-bottom: 1.5rem;
    animation: pulse 2s infinite;
}

.pending-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 1rem;
}

.pending-subtitle {
    font-size: 1.1rem;
    color: #7f8c8d;
    margin-bottom: 2rem;
    line-height: 1.6;
}

.order-details-card {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    text-align: left;
}

.order-details-card h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 1rem;
    text-align: center;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 0.75rem 0;
    border-bottom: 1px solid #e9ecef;
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 500;
    color: #495057;
    min-width: 120px;
}

.detail-value {
    text-align: right;
    flex: 1;
    margin-left: 1rem;
}

.total-amount {
    font-weight: 700;
    font-size: 1.1rem;
    color: #f39c12;
}

.payment-instructions {
    background: #fff3cd;
    padding: 1.5rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    text-align: left;
    border-left: 4px solid #f39c12;
}

.payment-instructions h4 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
}

.instruction-list {
    margin-top: 1rem;
}

.instruction-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 1.5rem;
    gap: 1rem;
}

.instruction-item:last-child {
    margin-bottom: 0;
}

.step-number {
    background: #f39c12;
    color: white;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.9rem;
    flex-shrink: 0;
}

.step-content strong {
    display: block;
    color: #2c3e50;
    margin-bottom: 0.25rem;
}

.step-content p {
    color: #495057;
    font-size: 0.9rem;
    margin-bottom: 0;
}

.payment-action {
    margin-bottom: 2rem;
}

.status-check {
    background: #e8f4f8;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 2rem;
}

.status-check p {
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.action-buttons .btn {
    padding: 0.75rem 2rem;
    font-weight: 600;
    border-radius: 8px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    min-width: 160px;
    justify-content: center;
}

/* Animation */
@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
    }
}

@media (max-width: 768px) {
    .pending-section {
        padding: 40px 0;
    }

    .pending-container {
        padding: 2rem;
        margin: 1rem;
    }

    .pending-title {
        font-size: 2rem;
    }

    .pending-icon {
        font-size: 3rem;
    }

    .detail-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
    }

    .detail-value {
        text-align: left;
        margin-left: 0;
    }

    .instruction-item {
        flex-direction: row;
        align-items: flex-start;
    }

    .action-buttons {
        flex-direction: column;
        align-items: center;
    }

    .action-buttons .btn {
        width: 100%;
        max-width: 250px;
    }
}
</style>
@endsection