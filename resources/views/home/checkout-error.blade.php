@extends('home.layout')

@section('title', 'Payment Error - Marketplace')

@section('content')

  <!-- Error Section -->
  <div class="error-section">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
          <div class="error-container">

            <!-- Error Icon -->
            <div class="error-icon">
              <i class="fas fa-times-circle"></i>
            </div>

            <!-- Error Message -->
            <h1 class="error-title">Payment Failed</h1>
            <p class="error-subtitle">
              Unfortunately, there was an issue processing your payment. Don't worry, your order is still reserved.
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
                    <span class="badge bg-danger">Failed</span>
                  </span>
                </div>
              </div>
            @endif

            <!-- Error Solutions -->
            <div class="error-solutions">
              <h4><i class="fas fa-lightbulb me-2"></i>What can you do?</h4>
              <div class="solution-list">
                <div class="solution-item">
                  <div class="solution-icon">
                    <i class="fas fa-redo"></i>
                  </div>
                  <div class="solution-content">
                    <strong>Try Again</strong>
                    <p>The issue might be temporary. Try processing your payment again.</p>
                  </div>
                </div>
                <div class="solution-item">
                  <div class="solution-icon">
                    <i class="fas fa-credit-card"></i>
                  </div>
                  <div class="solution-content">
                    <strong>Different Payment Method</strong>
                    <p>Try using a different payment method or card.</p>
                  </div>
                </div>
                <div class="solution-item">
                  <div class="solution-icon">
                    <i class="fas fa-phone"></i>
                  </div>
                  <div class="solution-content">
                    <strong>Contact Your Bank</strong>
                    <p>Check with your bank if there are any restrictions on your card.</p>
                  </div>
                </div>
                <div class="solution-item">
                  <div class="solution-icon">
                    <i class="fas fa-headset"></i>
                  </div>
                  <div class="solution-content">
                    <strong>Contact Support</strong>
                    <p>If the problem persists, contact our customer support team.</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Common Issues -->
            <div class="common-issues">
              <h5>Common Issues:</h5>
              <ul>
                <li>Insufficient funds in your account</li>
                <li>Expired or blocked card</li>
                <li>Incorrect card details</li>
                <li>Network connection problems</li>
                <li>Bank security restrictions</li>
              </ul>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
              @if($order)
                <a href="{{ route('checkout.payment', $order->order_number) }}" class="btn btn-primary btn-lg">
                  <i class="fas fa-redo me-2"></i>
                  Try Payment Again
                </a>
              @endif
              <a href="{{ route('checkout.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-shopping-cart me-2"></i>
                Back to Checkout
              </a>
              <a href="{{ route('shop') }}" class="btn btn-outline-secondary">
                <i class="fas fa-shopping-bag me-2"></i>
                Continue Shopping
              </a>
            </div>

            <!-- Support Contact -->
            <div class="support-contact">
              <p class="text-muted">
                <i class="fas fa-envelope me-2"></i>
                Need help? Contact us at <strong>support@marketplace.com</strong> or call <strong>1-800-SUPPORT</strong>
              </p>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('styles')
<style>
.error-section {
    padding: 80px 0;
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
    min-height: 80vh;
    display: flex;
    align-items: center;
}

.error-container {
    text-align: center;
    background: white;
    padding: 3rem;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

.error-icon {
    font-size: 4rem;
    color: #e74c3c;
    margin-bottom: 1.5rem;
    animation: shake 1s ease-in-out;
}

.error-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 1rem;
}

.error-subtitle {
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
    color: #e74c3c;
}

.error-solutions {
    background: #f8d7da;
    padding: 1.5rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    text-align: left;
    border-left: 4px solid #e74c3c;
}

.error-solutions h4 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
}

.solution-list {
    margin-top: 1rem;
}

.solution-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 1rem;
    gap: 1rem;
}

.solution-item:last-child {
    margin-bottom: 0;
}

.solution-icon {
    background: #e74c3c;
    color: white;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
    flex-shrink: 0;
}

.solution-content strong {
    display: block;
    color: #2c3e50;
    margin-bottom: 0.25rem;
    font-size: 0.95rem;
}

.solution-content p {
    color: #495057;
    font-size: 0.85rem;
    margin-bottom: 0;
}

.common-issues {
    background: #fff3cd;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 2rem;
    text-align: left;
    border-left: 4px solid #f39c12;
}

.common-issues h5 {
    font-size: 1rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.75rem;
}

.common-issues ul {
    margin-bottom: 0;
    padding-left: 1.5rem;
}

.common-issues li {
    margin-bottom: 0.25rem;
    color: #495057;
    font-size: 0.85rem;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
    margin-bottom: 2rem;
}

.action-buttons .btn {
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    border-radius: 8px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.support-contact {
    padding-top: 1rem;
    border-top: 1px solid #e9ecef;
}

.support-contact p {
    margin-bottom: 0;
    font-size: 0.9rem;
}

/* Animation */
@keyframes shake {
    0%, 100% {
        transform: translateX(0);
    }
    10%, 30%, 50%, 70%, 90% {
        transform: translateX(-5px);
    }
    20%, 40%, 60%, 80% {
        transform: translateX(5px);
    }
}

@media (max-width: 768px) {
    .error-section {
        padding: 40px 0;
    }

    .error-container {
        padding: 2rem;
        margin: 1rem;
    }

    .error-title {
        font-size: 2rem;
    }

    .error-icon {
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

    .solution-item {
        align-items: center;
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