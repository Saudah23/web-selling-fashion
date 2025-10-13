@extends('home.layout')

@section('title', 'Order Details - ' . $order->order_number)

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
            <a href="{{ route('orders.index') }}">My Orders</a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">{{ $order->order_number }}</li>
        </ol>
      </nav>
    </div>
  </div>

  <!-- Order Details Section -->
  <div class="order-details-section">
    <div class="container">
      <div class="row">
        <div class="col-12">

          <!-- Order Header -->
          <div class="order-header-card">
            <div class="order-info">
              <h2 class="order-title">
                <i class="fa fa-receipt me-2"></i>
                Order #{{ $order->order_number }}
              </h2>
              <div class="order-meta">
                <span class="order-date">
                  <i class="fa fa-calendar me-1"></i>
                  {{ $order->created_at->format('d M Y, H:i') }}
                </span>
                <span class="order-status">
                  @if(is_string($order->status_badge))
                    {!! $order->status_badge !!}
                  @else
                    <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                  @endif
                </span>
              </div>
            </div>
            <div class="order-actions">
              @if($order->status === 'pending')
                <div class="action-group">
                  @if($order->paymentTransaction && $order->paymentTransaction->status === 'pending')
                    <a href="{{ route('checkout.payment', $order->order_number) }}" class="action-btn action-btn-success" title="Pay Now">
                      <i class="fa fa-credit-card"></i>
                    </a>
                  @endif
                  <form method="POST" action="{{ route('orders.cancel', $order->order_number) }}"
                        class="action-form" onsubmit="return confirm('Are you sure you want to cancel this order?')">
                    @csrf
                    <button type="submit" class="action-btn action-btn-danger" title="Cancel Order">
                      <i class="fa fa-times"></i>
                    </button>
                  </form>
                </div>
              @endif


              @if(in_array($order->status, ['delivered']))
                <form method="POST" action="{{ route('orders.reorder', $order->order_number) }}" class="action-form">
                  @csrf
                  <button type="submit" class="action-btn action-btn-primary" title="Reorder">
                    <i class="fa fa-redo"></i>
                  </button>
                </form>
              @endif

              @if($order->status !== 'pending')
                <a href="{{ route('orders.invoice', $order->order_number) }}" class="action-btn" target="_blank" title="Download Invoice">
                  <i class="fa fa-file-pdf"></i>
                </a>
              @endif
            </div>
          </div>

          <div class="row">
            <!-- Left Column - Order Details -->
            <div class="col-lg-8">

              <!-- Order Items -->
              <div class="detail-card">
                <h4 class="card-title">
                  <i class="fa fa-box me-2"></i>
                  Order Items ({{ $order->items->count() }} items)
                </h4>
                <div class="order-items-list">
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
                          <p class="item-sku d-none d-md-block">SKU: {{ $item->product_sku }}</p>
                        @endif
                        @if($item->product_attributes && is_array($item->product_attributes))
                          <div class="item-attributes d-none d-md-flex">
                            @foreach($item->product_attributes as $key => $value)
                              <span class="attribute">{{ ucfirst($key) }}: {{ is_array($value) ? implode(', ', $value) : $value }}</span>
                            @endforeach
                          </div>
                        @elseif($item->product_attributes && is_string($item->product_attributes))
                          <div class="item-attributes d-none d-md-block">
                            <span class="attribute">{{ $item->product_attributes }}</span>
                          </div>
                        @endif
                        <div class="item-attributes-mobile d-md-none">
                          @if($item->product_sku)
                            <div class="attr-text">SKU: {{ $item->product_sku }}</div>
                          @endif
                          @if($item->product_attributes && is_array($item->product_attributes))
                            @foreach($item->product_attributes as $key => $value)
                              <div class="attr-text">{{ ucfirst($key) }}: {{ is_array($value) ? implode(', ', $value) : $value }}</div>
                            @endforeach
                          @endif
                        </div>
                        <div class="item-price-info">
                          <span class="item-price">{{ $item->formatted_product_price }}</span>
                          @if($item->product_compare_price && $item->discount_percentage)
                            <span class="item-compare-price">{{ 'Rp ' . number_format($item->product_compare_price, 0, ',', '.') }}</span>
                            <span class="discount-badge">-{{ $item->discount_percentage }}%</span>
                          @endif
                        </div>
                      </div>
                      <div class="item-quantity">
                        <span class="quantity-label">Qty:</span>
                        <span class="quantity-value">{{ $item->quantity }}</span>
                      </div>
                      <div class="item-subtotal">
                        {{ $item->formatted_subtotal }}
                      </div>
                    </div>
                  @endforeach
                </div>
              </div>

              <!-- Shipping Information -->
              <div class="detail-card">
                <h4 class="card-title">
                  <i class="fa fa-truck me-2"></i>
                  Shipping Information
                </h4>
                <div class="shipping-details">
                  <div class="shipping-address">
                    <h6>Delivery Address</h6>
                    <div class="address-info">
                      <p class="recipient-name">{{ $order->shipping_address['recipient_name'] }}</p>
                      <p class="phone">{{ $order->shipping_address['phone'] }}</p>
                      <p class="address">{{ $order->shipping_address['address'] }}</p>
                      <p class="location">
                        {{ $order->shipping_address['village_name'] }},
                        {{ $order->shipping_address['district_name'] }},<br>
                        {{ $order->shipping_address['city_name'] }},
                        {{ $order->shipping_address['province_name'] }}
                        {{ $order->shipping_address['postal_code'] }}
                      </p>
                    </div>
                  </div>
                  <div class="shipping-service">
                    <h6>Shipping Service</h6>
                    <div class="courier-info">
                      @php
                        $courier = strtolower($order->shipping_courier ?? '');
                        $courierLogos = [
                          'jne' => 'https://cdn.jsdelivr.net/gh/naldoreuben/indonesia-shipping-logo@main/jne.png',
                          'pos' => 'https://cdn.jsdelivr.net/gh/naldoreuben/indonesia-shipping-logo@main/pos.png',
                          'tiki' => 'https://cdn.jsdelivr.net/gh/naldoreuben/indonesia-shipping-logo@main/tiki.png',
                          'jnt' => 'https://cdn.jsdelivr.net/gh/naldoreuben/indonesia-shipping-logo@main/jnt.png',
                          'sicepat' => 'https://cdn.jsdelivr.net/gh/naldoreuben/indonesia-shipping-logo@main/sicepat.png',
                          'anteraja' => 'https://cdn.jsdelivr.net/gh/naldoreuben/indonesia-shipping-logo@main/anteraja.png'
                        ];
                        $logoUrl = $courierLogos[$courier] ?? null;
                      @endphp
                      @if($logoUrl)
                        <div class="courier-logo">
                          <img src="{{ $logoUrl }}" alt="{{ strtoupper($courier) }}" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline'">
                          <span style="display:none">{{ strtoupper($courier) }}</span>
                        </div>
                      @endif
                      <div class="service-details">
                        <span class="service-name">{{ $order->shipping_service }}</span>
                        @if($order->shipping_etd)
                          <small class="text-muted">({{ $order->shipping_etd }})</small>
                        @endif
                      </div>
                    </div>
                    @if($order->tracking_number)
                      <div class="tracking-info">
                        <strong>Tracking:</strong>
                        <code>{{ $order->tracking_number }}</code>
                      </div>
                    @endif
                  </div>
                </div>
              </div>

              <!-- Payment Information -->
              @if($order->paymentTransaction)
                <div class="detail-card">
                  <h4 class="card-title">
                    <i class="fa fa-credit-card me-2"></i>
                    Payment Information
                  </h4>
                  <div class="payment-details">
                    <div class="payment-status">
                      <span class="status-label">Payment Status:</span>
                      @if(is_string($order->paymentTransaction->status_badge))
                        {!! $order->paymentTransaction->status_badge !!}
                      @else
                        <span class="badge bg-warning">{{ ucfirst($order->paymentTransaction->status) }}</span>
                      @endif
                    </div>
                    @if($order->paymentTransaction->payment_type)
                      <div class="payment-method">
                        <span class="method-label">Payment Method:</span>
                        <span class="method-value">{{ ucfirst(str_replace('_', ' ', $order->paymentTransaction->payment_type)) }}</span>
                      </div>
                    @endif
                    @if($order->paymentTransaction->transaction_id)
                      <div class="transaction-id">
                        <span class="transaction-label">Transaction ID:</span>
                        <code>{{ $order->paymentTransaction->transaction_id }}</code>
                      </div>
                    @endif
                    @if($order->paymentTransaction->settlement_time)
                      <div class="payment-time">
                        <span class="time-label">Payment Time:</span>
                        <span class="time-value">{{ $order->paymentTransaction->settlement_time->format('d M Y, H:i') }}</span>
                      </div>
                    @endif
                  </div>
                </div>
              @endif

              <!-- Order Notes -->
              @if($order->notes)
                <div class="detail-card">
                  <h4 class="card-title">
                    <i class="fa fa-comment me-2"></i>
                    Order Notes
                  </h4>
                  <p class="order-notes">{{ $order->notes }}</p>
                </div>
              @endif

            </div>

            <!-- Right Column - Order Summary -->
            <div class="col-lg-4">
              <div class="order-summary-card">
                <h4 class="card-title">
                  <i class="fa fa-calculator me-2"></i>
                  Order Summary
                </h4>
                <div class="summary-details">
                  <div class="summary-row">
                    <span>Subtotal ({{ $order->items->count() }} items)</span>
                    <span>{{ $order->formatted_subtotal }}</span>
                  </div>
                  <div class="summary-row">
                    <span>Shipping Cost</span>
                    <span>{{ $order->formatted_shipping_cost }}</span>
                  </div>
                  @if($order->tax_amount > 0)
                    <div class="summary-row">
                      <span>Tax</span>
                      <span>{{ 'Rp ' . number_format($order->tax_amount, 0, ',', '.') }}</span>
                    </div>
                  @endif
                  <div class="summary-row total-row">
                    <span><strong>Total</strong></span>
                    <span><strong>{{ $order->formatted_total_amount }}</strong></span>
                  </div>
                </div>

                <!-- Order Timeline -->
                <div class="order-timeline">
                  <h6>Order Timeline</h6>
                  <div class="timeline">
                    <div class="timeline-item {{ $order->created_at ? 'completed' : '' }}">
                      <div class="timeline-icon">
                        <i class="fa fa-shopping-cart"></i>
                      </div>
                      <div class="timeline-content">
                        <h6>Order Placed</h6>
                        @if($order->created_at)
                          <p>{{ $order->created_at->format('d M Y, H:i') }}</p>
                        @endif
                      </div>
                    </div>

                    <div class="timeline-item {{ $order->paid_at ? 'completed' : ($order->status === 'pending' ? 'current' : '') }}">
                      <div class="timeline-icon">
                        <i class="fa fa-credit-card"></i>
                      </div>
                      <div class="timeline-content">
                        <h6>Payment Confirmed</h6>
                        @if($order->paid_at)
                          <p>{{ $order->paid_at->format('d M Y, H:i') }}</p>
                        @else
                          <p>Waiting for payment</p>
                        @endif
                      </div>
                    </div>

                    <div class="timeline-item {{ in_array($order->status, ['processing', 'shipped', 'delivered']) ? 'completed' : '' }}">
                      <div class="timeline-icon">
                        <i class="fa fa-cogs"></i>
                      </div>
                      <div class="timeline-content">
                        <h6>Processing</h6>
                        <p>Order being prepared</p>
                      </div>
                    </div>

                    <div class="timeline-item {{ $order->shipped_at ? 'completed' : ($order->status === 'shipped' ? 'current' : '') }}">
                      <div class="timeline-icon">
                        <i class="fa fa-truck"></i>
                      </div>
                      <div class="timeline-content">
                        <h6>Shipped</h6>
                        @if($order->shipped_at)
                          <p>{{ $order->shipped_at->format('d M Y, H:i') }}</p>
                        @else
                          <p>Waiting to ship</p>
                        @endif
                      </div>
                    </div>

                    <div class="timeline-item {{ $order->delivered_at ? 'completed' : ($order->status === 'delivered' ? 'current' : '') }}">
                      <div class="timeline-icon">
                        <i class="fa fa-check-circle"></i>
                      </div>
                      <div class="timeline-content">
                        <h6>Delivered</h6>
                        @if($order->delivered_at)
                          <p>{{ $order->delivered_at->format('d M Y, H:i') }}</p>
                        @else
                          <p>Waiting for delivery</p>
                        @endif
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

@endsection

@section('scripts')
@endsection

@section('styles')
<style>
.order-details-section {
    padding: 0.75rem 0;
    background-color: #f8f9fa;
}

.order-header-card {
    background: white;
    border-radius: 0.25rem;
    padding: 0.75rem;
    margin-bottom: 0.75rem;
    border: 1px solid #dee2e6;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.order-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #212529;
    margin-bottom: 0.25rem;
}

.order-meta {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.order-date {
    color: #7f8c8d;
    font-size: 0.95rem;
}

.order-actions {
    display: flex;
    gap: 0.5rem;
}

.detail-card, .order-summary-card {
    background: white;
    border-radius: 0.25rem;
    padding: 0.75rem;
    margin-bottom: 0.75rem;
    border: 1px solid #dee2e6;
}

.card-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: #212529;
    margin-bottom: 0.5rem;
    border-bottom: 1px solid #f1f3f4;
    padding-bottom: 0.25rem;
}

.card-title i {
    color: #3498db;
}

.order-items-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.order-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem;
    background: #f8f9fa;
    border-radius: 0.25rem;
    border: 1px solid #e9ecef;
}

.item-image {
    width: 50px;
    height: 50px;
    border-radius: 0.25rem;
    overflow: hidden;
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
    font-size: 1rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.item-sku {
    font-size: 0.8rem;
    color: #7f8c8d;
    margin-bottom: 0.5rem;
}

.item-attributes {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}

.attribute {
    background: #e9ecef;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.8rem;
    color: #495057;
}

.item-price-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.item-price {
    font-weight: 600;
    color: #27ae60;
}

.item-compare-price {
    text-decoration: line-through;
    color: #7f8c8d;
    font-size: 0.9rem;
}

.discount-badge {
    background: #e74c3c;
    color: white;
    padding: 0.125rem 0.375rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
}

.item-quantity {
    text-align: center;
    min-width: 60px;
}

.quantity-label {
    display: block;
    font-size: 0.8rem;
    color: #7f8c8d;
}

.quantity-value {
    display: block;
    font-size: 1.1rem;
    font-weight: 600;
    color: #2c3e50;
}

.item-subtotal {
    text-align: right;
    min-width: 100px;
    font-size: 1.1rem;
    font-weight: 600;
    color: #27ae60;
}

.shipping-details {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.shipping-address h6, .shipping-service h6 {
    color: #2c3e50;
    margin-bottom: 1rem;
    font-weight: 600;
}

.address-info {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    border-left: 4px solid #3498db;
}

.address-info p {
    margin-bottom: 0.5rem;
    line-height: 1.4;
}

.recipient-name {
    font-weight: 600;
    color: #2c3e50;
}

.phone {
    color: #7f8c8d;
}

.tracking-info {
    margin-top: 1rem;
    padding: 0.75rem;
    background: #e8f5e8;
    border-radius: 6px;
    border-left: 4px solid #27ae60;
}

.tracking-info code {
    background: #27ae60;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
}

.payment-details {
    display: grid;
    gap: 1rem;
}

.payment-status, .payment-method, .transaction-id, .payment-time {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 6px;
}

.payment-status .status-label,
.payment-method .method-label,
.transaction-id .transaction-label,
.payment-time .time-label {
    font-weight: 500;
    color: #495057;
}

.order-notes {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    border-left: 4px solid #f39c12;
    margin: 0;
    line-height: 1.6;
}

.summary-details {
    margin-bottom: 2rem;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #e9ecef;
}

.summary-row:last-child {
    border-bottom: none;
}

.total-row {
    font-size: 1.1rem;
    border-top: 2px solid #e9ecef;
    margin-top: 1rem;
    padding-top: 1rem;
    color: #27ae60;
}

.order-timeline h6 {
    color: #2c3e50;
    margin-bottom: 1rem;
    font-weight: 600;
}

.timeline {
    position: relative;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    padding-left: 3rem;
    margin-bottom: 2rem;
}

.timeline-item:last-child {
    margin-bottom: 0;
}

.timeline-icon {
    position: absolute;
    left: 0;
    top: 0;
    width: 32px;
    height: 32px;
    background: #e9ecef;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #7f8c8d;
    font-size: 0.875rem;
}

.timeline-item.completed .timeline-icon {
    background: #27ae60;
    color: white;
}

.timeline-item.current .timeline-icon {
    background: #3498db;
    color: white;
    animation: pulse 2s infinite;
}

.timeline-content h6 {
    margin-bottom: 0.25rem;
    font-size: 0.9rem;
    font-weight: 600;
}

.timeline-content p {
    margin: 0;
    font-size: 0.8rem;
    color: #7f8c8d;
}

.timeline-item.completed .timeline-content h6 {
    color: #27ae60;
}

.timeline-item.current .timeline-content h6 {
    color: #3498db;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(52, 152, 219, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(52, 152, 219, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(52, 152, 219, 0);
    }
}

/* Action Buttons */
.order-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.action-group {
    display: flex;
    align-items: center;
    gap: 0.375rem;
}

.action-form {
    display: inline-block;
    margin: 0;
}

.action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    font-size: 0.75rem;
    text-decoration: none;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    color: #6c757d;
    background-color: white;
    transition: all 0.2s;
    cursor: pointer;
}

.action-btn:hover {
    color: #495057;
    border-color: #adb5bd;
    text-decoration: none;
}

.action-btn-success {
    color: white;
    background-color: #28a745;
    border-color: #28a745;
}

.action-btn-success:hover {
    color: white;
    background-color: #218838;
    border-color: #1e7e34;
}

.action-btn-danger {
    color: white;
    background-color: #dc3545;
    border-color: #dc3545;
}

.action-btn-danger:hover {
    color: white;
    background-color: #c82333;
    border-color: #bd2130;
}

.action-btn-info {
    color: white;
    background-color: #17a2b8;
    border-color: #17a2b8;
}

.action-btn-info:hover {
    color: white;
    background-color: #138496;
    border-color: #117a8b;
}

.action-btn-primary {
    color: white;
    background-color: #007bff;
    border-color: #007bff;
}

.action-btn-primary:hover {
    color: white;
    background-color: #0069d9;
    border-color: #0062cc;
}

/* Courier Logo Styles */
.courier-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.courier-logo img {
    height: 24px;
    width: auto;
    max-width: 60px;
    object-fit: contain;
}

.service-details {
    display: flex;
    flex-direction: column;
}

.service-name {
    font-size: 0.875rem;
    font-weight: 500;
    color: #212529;
}

.tracking-info {
    margin-top: 0.5rem;
    padding: 0.375rem 0.5rem;
    background: #e8f5e8;
    border-radius: 0.25rem;
    border-left: 3px solid #28a745;
    font-size: 0.8rem;
}

.tracking-info code {
    background: #28a745;
    color: white;
    padding: 0.125rem 0.375rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
}

@media (max-width: 768px) {
    .order-details-section {
        padding: 0.25rem 0;
    }

    .order-header-card {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
        padding: 0.375rem 0.5rem;
        margin-bottom: 0.375rem;
        border-radius: 0.25rem;
    }

    .order-title {
        font-size: 0.75rem;
        margin-bottom: 0.125rem;
    }

    .order-meta {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        flex-wrap: wrap;
    }

    .order-date {
        font-size: 0.65rem;
    }

    .order-actions {
        width: 100%;
        display: flex;
        gap: 0.375rem;
        align-items: center;
        margin-top: 0.375rem;
        flex-wrap: wrap;
    }

    .action-group {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .action-btn {
        width: 24px;
        height: 24px;
        font-size: 0.65rem;
        flex-shrink: 0;
    }

    .detail-card, .order-summary-card {
        padding: 0.375rem 0.5rem;
        margin-bottom: 0.375rem;
        border-radius: 0.25rem;
    }

    .card-title {
        font-size: 0.7rem;
        margin-bottom: 0.25rem;
        padding-bottom: 0.125rem;
        font-weight: 700;
    }

    .order-items-list {
        gap: 0.375rem;
    }

    .order-item {
        padding: 0.375rem 0.25rem;
        gap: 0.375rem;
        border-radius: 0.25rem;
        min-height: 60px;
        align-items: flex-start;
    }

    .item-image {
        width: 32px;
        height: 32px;
        border-radius: 0.25rem;
    }

    .item-details {
        flex: 1;
        min-width: 0;
    }

    .item-name {
        font-size: 0.65rem;
        font-weight: 600;
        margin-bottom: 0.125rem;
        line-height: 1.2;
    }

    .item-sku {
        font-size: 0.55rem;
        margin-bottom: 0.125rem;
    }

    .item-attributes {
        gap: 0.25rem;
        margin-bottom: 0.25rem;
    }

    .attribute {
        padding: 0.125rem 0.25rem;
        font-size: 0.55rem;
        border-radius: 0.25rem;
    }

    .item-price-info {
        gap: 0.25rem;
        font-size: 0.65rem;
        align-items: center;
    }

    .item-price {
        font-size: 0.65rem;
        font-weight: 600;
    }

    .item-compare-price {
        font-size: 0.6rem;
        text-decoration: line-through;
        color: #6c757d;
    }

    .discount-badge {
        font-size: 0.5rem;
        padding: 0.125rem 0.25rem;
        border-radius: 0.25rem;
        background-color: #dc3545;
        color: white;
        font-weight: 600;
        line-height: 1;
    }

    .item-quantity {
        min-width: 30px;
        text-align: center;
    }

    .quantity-label {
        font-size: 0.55rem;
    }

    .quantity-value {
        font-size: 0.7rem;
        font-weight: 600;
    }

    .item-subtotal {
        min-width: 60px;
        font-size: 0.65rem;
        font-weight: 600;
        text-align: right;
        color: #28a745;
    }

    .shipping-details {
        grid-template-columns: 1fr;
        gap: 0.5rem;
    }

    .shipping-address h6, .shipping-service h6 {
        font-size: 0.7rem;
        margin-bottom: 0.25rem;
        font-weight: 700;
    }

    .address-info {
        padding: 0.375rem;
        border-radius: 0.25rem;
    }

    .address-info p {
        margin-bottom: 0.25rem;
        font-size: 0.65rem;
        line-height: 1.3;
    }

    .courier-info {
        gap: 0.25rem;
    }

    .courier-logo img {
        height: 16px;
        max-width: 40px;
    }

    .service-name {
        font-size: 0.65rem;
        font-weight: 500;
    }

    .tracking-info {
        margin-top: 0.25rem;
        padding: 0.25rem 0.375rem;
        font-size: 0.6rem;
    }

    .tracking-info code {
        padding: 0.125rem 0.25rem;
        font-size: 0.55rem;
    }

    .payment-details {
        gap: 0.5rem;
    }

    .payment-status, .payment-method, .transaction-id, .payment-time {
        padding: 0.375rem;
        font-size: 0.65rem;
        border-radius: 0.25rem;
    }

    .order-notes {
        padding: 0.375rem;
        font-size: 0.65rem;
        line-height: 1.4;
        border-radius: 0.25rem;
    }

    .summary-details {
        margin-bottom: 1rem;
    }

    .summary-row {
        padding: 0.375rem 0;
        font-size: 0.65rem;
        border-bottom: 1px solid #e9ecef;
    }

    .total-row {
        font-size: 0.7rem;
        padding-top: 0.5rem;
        margin-top: 0.5rem;
    }

    .order-timeline h6 {
        font-size: 0.7rem;
        margin-bottom: 0.5rem;
        font-weight: 700;
    }

    .timeline::before {
        left: 10px;
        width: 1px;
    }

    .timeline-item {
        padding-left: 1.5rem;
        margin-bottom: 0.75rem;
    }

    .timeline-icon {
        width: 20px;
        height: 20px;
        font-size: 0.6rem;
        left: 0;
    }

    .timeline-content h6 {
        font-size: 0.65rem;
        font-weight: 600;
        margin-bottom: 0.125rem;
    }

    .timeline-content p {
        font-size: 0.6rem;
    }

    .item-attributes-mobile {
        margin-top: 0.25rem;
    }

    .attr-text {
        font-size: 0.55rem;
        color: #6c757d;
        margin-bottom: 0.125rem;
        line-height: 1.2;
    }
}
</style>
@endsection