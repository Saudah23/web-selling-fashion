@extends('home.layout')

@section('title', 'My Orders - Marketplace')

@section('content')

  <!-- Breadcrumb Section -->
  <div class="breadcrumb-section">
    <div class="container">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ route('home') }}">Home</a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">My Orders</li>
        </ol>
      </nav>
    </div>
  </div>

  <!-- Orders Section -->
  <div class="orders-section">
    <div class="container">
      <div class="orders-header mb-4">
        <h2 class="section-title">My Orders</h2>
        <p class="text-muted">Track and manage your orders</p>
      </div>

      <div class="row">
        <div class="col-lg-8">
          <!-- Filter Tabs -->
          <div class="order-filters mb-4">
            <!-- Desktop Filter Navigation -->
            <div class="filter-nav d-none d-md-flex">
              <a class="filter-link {{ !$statusFilter ? 'active' : '' }}" href="{{ route('orders.index') }}">
                All ({{ $statusCounts['all'] }})
              </a>
              <a class="filter-link {{ $statusFilter === 'pending' ? 'active' : '' }}" href="{{ route('orders.index', ['status' => 'pending']) }}">
                Pending ({{ $statusCounts['pending'] }})
              </a>
              <a class="filter-link {{ $statusFilter === 'paid' ? 'active' : '' }}" href="{{ route('orders.index', ['status' => 'paid']) }}">
                Paid ({{ $statusCounts['paid'] }})
              </a>
              <a class="filter-link {{ $statusFilter === 'processing' ? 'active' : '' }}" href="{{ route('orders.index', ['status' => 'processing']) }}">
                Processing ({{ $statusCounts['processing'] }})
              </a>
              <a class="filter-link {{ $statusFilter === 'shipped' ? 'active' : '' }}" href="{{ route('orders.index', ['status' => 'shipped']) }}">
                Shipped ({{ $statusCounts['shipped'] }})
              </a>
              <a class="filter-link {{ $statusFilter === 'delivered' ? 'active' : '' }}" href="{{ route('orders.index', ['status' => 'delivered']) }}">
                Delivered ({{ $statusCounts['delivered'] }})
              </a>
            </div>

            <!-- Mobile Filter Dropdown -->
            <div class="filter-dropdown d-md-none">
              <select class="form-select" id="statusFilter" onchange="window.location.href = this.value">
                <option value="{{ route('orders.index') }}" {{ !$statusFilter ? 'selected' : '' }}>
                  All Orders ({{ $statusCounts['all'] }})
                </option>
                <option value="{{ route('orders.index', ['status' => 'pending']) }}" {{ $statusFilter === 'pending' ? 'selected' : '' }}>
                  Pending ({{ $statusCounts['pending'] }})
                </option>
                <option value="{{ route('orders.index', ['status' => 'paid']) }}" {{ $statusFilter === 'paid' ? 'selected' : '' }}>
                  Paid ({{ $statusCounts['paid'] }})
                </option>
                <option value="{{ route('orders.index', ['status' => 'processing']) }}" {{ $statusFilter === 'processing' ? 'selected' : '' }}>
                  Processing ({{ $statusCounts['processing'] }})
                </option>
                <option value="{{ route('orders.index', ['status' => 'shipped']) }}" {{ $statusFilter === 'shipped' ? 'selected' : '' }}>
                  Shipped ({{ $statusCounts['shipped'] }})
                </option>
                <option value="{{ route('orders.index', ['status' => 'delivered']) }}" {{ $statusFilter === 'delivered' ? 'selected' : '' }}>
                  Delivered ({{ $statusCounts['delivered'] }})
                </option>
              </select>
            </div>
          </div>

          <!-- Search Box -->
          <div class="search-box mb-4">
            <form method="GET" action="{{ route('orders.index') }}">
              @if($statusFilter)
                <input type="hidden" name="status" value="{{ $statusFilter }}">
              @endif
              <div class="input-group">
                <input type="text" class="form-control" name="search"
                       placeholder="Search orders..."
                       value="{{ $search }}">
                <button class="btn btn-outline-secondary" type="submit">
                  <i class="fa fa-search"></i>
                </button>
              </div>
            </form>
          </div>

          <!-- Orders List -->
          @if($orders->count() > 0)
            <div class="orders-list">
              @foreach($orders as $order)
                <div class="order-item-list">
                  <div class="order-main-info">
                    <div class="order-id-date">
                      <div class="order-number-small">{{ $order->order_number }}</div>
                      <small class="text-muted">{{ $order->created_at->format('d M Y, H:i') }}</small>
                    </div>
                    <div class="order-status-price">
                      @if(is_string($order->status_badge))
                        {!! $order->status_badge !!}
                      @else
                        <span class="status-badge status-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                      @endif
                      <div class="order-total-small">{{ $order->formatted_total_amount }}</div>
                    </div>
                  </div>

                  <div class="order-items-preview">
                    @foreach($order->items->take(1) as $item)
                      <div class="item-preview">
                        <div class="item-image-tiny">
                          @if($item->product_image)
                            <img src="{{ asset('storage/' . $item->product_image) }}"
                                 alt="{{ $item->product_name }}"
                                 onerror="this.src='{{ asset('furni-1.0.0/images/product-1.png') }}';">
                          @else
                            <img src="{{ asset('furni-1.0.0/images/product-1.png') }}"
                                 alt="{{ $item->product_name }}">
                          @endif
                        </div>
                        <div class="item-info-tiny">
                          <div class="item-name-tiny">{{ $item->product_name }}</div>
                          @if($order->items->count() > 1)
                            <small class="text-muted">+{{ $order->items->count() - 1 }} more</small>
                          @endif
                        </div>
                      </div>
                    @endforeach
                  </div>

                  <div class="order-actions-list">
                    <a href="{{ route('orders.show', $order->order_number) }}" class="action-btn" title="View Details">
                      <i class="fa fa-eye"></i>
                    </a>
                    @if($order->status === 'pending' && $order->paymentTransaction && $order->paymentTransaction->status === 'pending')
                      <a href="{{ route('checkout.payment', $order->order_number) }}" class="action-btn action-btn-success" title="Pay Now">
                        <i class="fa fa-credit-card"></i>
                      </a>
                    @endif
                    @if($order->status === 'shipped')
                      <button type="button" class="action-btn action-btn-success" title="Mark as Delivered" onclick="markAsDelivered('{{ $order->order_number }}')">
                        <i class="fa fa-check"></i>
                      </button>
                    @endif
                  </div>
                </div>
              @endforeach
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
              {{ $orders->appends(request()->query())->links() }}
            </div>
          @else
            <div class="empty-orders text-center py-5">
              <i class="fa fa-shopping-bag fa-3x text-muted mb-3"></i>
              <h5>No Orders Found</h5>
              <p class="text-muted">
                @if($statusFilter || $search)
                  No orders match your current filters.
                @else
                  You haven't placed any orders yet.
                @endif
              </p>
              @if(!$statusFilter && !$search)
                <a href="{{ route('home') }}" class="action-btn action-btn-primary">
                  Start Shopping
                </a>
              @else
                <a href="{{ route('orders.index') }}" class="action-btn">
                  Clear Filters
                </a>
              @endif
            </div>
          @endif
        </div>

        <!-- Right Column - Recommendations -->
        <div class="col-lg-4">
          <h6 class="mb-3 text-muted">You might also like</h6>
          <div class="recommended-list">
            @foreach($recommendedProducts as $product)
              <div class="product-item">
                <div class="product-image-small">
                  @if($product->images->where('is_primary', true)->first())
                    <img src="{{ asset('storage/' . $product->images->where('is_primary', true)->first()->file_path) }}"
                         alt="{{ $product->name }}"
                         onerror="this.src='{{ asset('furni-1.0.0/images/product-1.png') }}';">
                  @else
                    <img src="{{ asset('furni-1.0.0/images/product-1.png') }}" alt="{{ $product->name }}">
                  @endif
                </div>
                <div class="product-details">
                  <div class="product-name-small">{{ $product->name }}</div>
                  <div class="product-price-small">{{ $product->formatted_price }}</div>
                </div>
                <a href="{{ route('product.detail', $product->id) }}" class="product-view-btn" title="View Product">
                  <i class="fa fa-eye"></i>
                </a>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('styles')
<style>
.orders-section {
    padding: 2rem 0;
    background-color: #f8f9fa;
}

.orders-header {
    margin-bottom: 2rem;
}

.section-title {
    font-size: 1.75rem;
    font-weight: 600;
    color: #212529;
    margin-bottom: 0.25rem;
}

/* Filter Navigation */
.filter-nav {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    border-bottom: 1px solid #dee2e6;
    padding-bottom: 0.5rem;
}

.filter-link {
    padding: 0.5rem 1rem;
    color: #6c757d;
    text-decoration: none;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    transition: all 0.2s;
}

.filter-link:hover {
    color: #495057;
    background-color: #e9ecef;
}

.filter-link.active {
    color: #0d6efd;
    background-color: #e7f1ff;
    font-weight: 500;
}

/* Order List Items */
.order-item-list {
    background: white;
    border: 1px solid #f1f3f4;
    border-radius: 0.25rem;
    margin-bottom: 0.5rem;
    padding: 0.75rem;
    transition: all 0.2s;
}

.order-item-list:hover {
    border-color: #dee2e6;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.order-main-info {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.5rem;
}

.order-number-small {
    font-size: 0.8rem;
    font-weight: 600;
    color: #212529;
}

.order-status-price {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.order-total-small {
    font-size: 0.8rem;
    font-weight: 600;
    color: #28a745;
}

.order-items-preview {
    margin-bottom: 0.5rem;
}

.item-preview {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.item-image-tiny {
    width: 32px;
    height: 32px;
    border-radius: 0.25rem;
    overflow: hidden;
    flex-shrink: 0;
}

.item-image-tiny img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.item-info-tiny {
    flex: 1;
    min-width: 0;
}

.item-name-tiny {
    font-size: 0.75rem;
    font-weight: 500;
    color: #212529;
    margin-bottom: 0.125rem;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
}

.order-actions-list {
    display: flex;
    gap: 0.5rem;
    justify-content: flex-end;
}

/* Action Buttons */
.action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    font-size: 0.875rem;
    text-decoration: none;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    color: #6c757d;
    background-color: white;
    transition: all 0.2s;
}

.action-btn:hover {
    color: #495057;
    border-color: #adb5bd;
    text-decoration: none;
}

.action-btn-primary {
    color: white;
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.action-btn-primary:hover {
    color: white;
    background-color: #0b5ed7;
    border-color: #0a58ca;
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


/* Status Badges */
.status-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-pending { background-color: #fff3cd; color: #664d03; }
.status-paid { background-color: #d1ecf1; color: #0c5460; }
.status-processing { background-color: #cce5ff; color: #004085; }
.status-shipped { background-color: #f8d7da; color: #721c24; }
.status-delivered { background-color: #d4edda; color: #155724; }
.status-cancelled { background-color: #f5c6cb; color: #721c24; }

/* Recommendations */
.recommended-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.product-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: white;
    border: 1px solid #f1f3f4;
    border-radius: 0.25rem;
    transition: all 0.2s;
}

.product-item:hover {
    border-color: #dee2e6;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.product-image-small {
    width: 40px;
    height: 40px;
    border-radius: 0.25rem;
    overflow: hidden;
    flex-shrink: 0;
}

.product-image-small img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-details {
    flex: 1;
    min-width: 0;
}

.product-name-small {
    font-size: 0.75rem;
    font-weight: 500;
    color: #212529;
    margin-bottom: 0.125rem;
    line-height: 1.2;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.product-price-small {
    font-size: 0.75rem;
    color: #28a745;
    font-weight: 600;
}

.product-view-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    font-size: 0.75rem;
    color: #6c757d;
    text-decoration: none;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    background-color: white;
    transition: all 0.2s;
    flex-shrink: 0;
}

.product-view-btn:hover {
    color: #0d6efd;
    border-color: #0d6efd;
    text-decoration: none;
}

@media (max-width: 768px) {
    .orders-section {
        padding: 1rem 0;
    }

    .section-title {
        font-size: 1.5rem;
    }

    .filter-dropdown .form-select {
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
    }

    .order-main-info {
        flex-direction: column;
        align-items: stretch;
        gap: 0.5rem;
    }

    .order-actions-list {
        justify-content: flex-start;
    }

    .recommended-list {
        margin-top: 2rem;
    }
}
</style>
@endsection

@section('scripts')
<script>
function markAsDelivered(orderNumber) {
    Notiflix.Confirm.show(
        'Confirm Delivery',
        'Are you sure you want to mark this order as delivered?',
        'Yes',
        'Cancel',
        function okCb() {
            // Show loading
            Notiflix.Loading.circle('Processing...');

            fetch(`/orders/${orderNumber}/mark-delivered`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                Notiflix.Loading.remove();

                if (data.success) {
                    Notiflix.Notify.success(data.message || 'Order marked as delivered successfully!');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    Notiflix.Notify.failure(data.message || 'Failed to mark order as delivered');
                }
            })
            .catch(error => {
                Notiflix.Loading.remove();
                console.error('Error:', error);
                Notiflix.Notify.failure('An error occurred. Please try again.');
            });
        },
        function cancelCb() {
            // User cancelled
        },
        {
            width: '320px',
            borderRadius: '8px',
        }
    );
}
</script>
@endsection