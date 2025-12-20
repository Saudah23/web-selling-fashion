@extends('home.layout')

@section('title', 'Checkout - FASHION SAAZZ')

@section('content')

  <!-- Breadcrumb Section -->
  <div class="breadcrumb-section">
    <div class="container">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ route('home') }}">Beranda</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ route('cart.index') }}">Keranjang</a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">Checkout</li>
        </ol>
      </nav>
    </div>
  </div>

  <!-- Checkout Section -->
  <div class="checkout-section">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <h2 class="section-title">Checkout</h2>
        </div>
      </div>

      <div class="row">
        <!-- Checkout Form -->
        <div class="col-lg-8">
          <form id="checkoutForm">
            @csrf

            <!-- Shipping Address Section -->
            <div class="checkout-section-card">
              <h4 class="checkout-section-title">
                <i class="fa fa-map-marker-alt me-2"></i>
                Alamat Pengiriman
              </h4>

              <div class="address-selection">
                @foreach($addresses as $address)
                  <div class="address-option">
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="address_id" id="address_{{ $address->id }}"
                        value="{{ $address->id }}" {{ $address->is_default ? 'checked' : '' }}>
                      <label class="form-check-label" for="address_{{ $address->id }}">
                        <div class="address-details">
                          <div class="address-header">
                            <strong>{{ $address->recipient_name }}</strong>
                            @if($address->is_default)
                              <span class="badge bg-primary ms-2">Utama</span>
                            @endif
                          </div>
                          <div class="address-body">
                            <p class="mb-1">{{ $address->phone }}</p>
                            <p class="mb-1">{{ $address->address }}</p>
                            <p class="mb-0 text-muted">
                              {{ $address->village->name }}, {{ $address->district->name }},
                              {{ $address->city->name }}, {{ $address->province->name }}
                              {{ $address->postal_code }}
                            </p>
                          </div>
                        </div>
                      </label>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>

            <!-- Shipping Options Section -->
            <div class="checkout-section-card" id="shippingSection" style="display: none;">
              <h4 class="checkout-section-title">
                <i class="fa fa-truck me-2"></i>
                Pilih Kurir
                <span class="text-muted fs-6" id="shippingDestination"></span>
              </h4>

              <!-- Shipping Info -->
              <div class="shipping-info-banner" id="shippingInfo" style="display: none;">
                <div class="shipping-detail">
                  <div class="shipping-detail-item">
                    <i class="fa fa-weight-hanging text-primary"></i>
                    <span>Berat Total: <strong id="totalWeight">-</strong> gram</span>
                  </div>
                  <div class="shipping-detail-item">
                    <i class="fa fa-map-marker-alt text-success"></i>
                    <span>Tujuan: <strong id="destinationCity">-</strong></span>
                  </div>
                  <div class="shipping-detail-item">
                    <i class="fa fa-store text-info"></i>
                    <span>Asal: <strong>Tanah Laut</strong></span>
                  </div>
                </div>
              </div>

              <div id="shippingOptions">
                <div class="text-center py-4">
                  <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Memuat opsi pengiriman...</span>
                  </div>
                  <p class="mt-2 text-muted">Menghitung ongkos kirim via RajaOngkir...</p>
                </div>
              </div>
            </div>

            <!-- Order Notes Section -->
            <div class="checkout-section-card">
              <h4 class="checkout-section-title">
                <i class="fa fa-comment me-2"></i>
                Catatan Pesanan (Opsional)
              </h4>

              <div class="form-group">
                <textarea class="form-control" name="notes" id="notes" rows="3"
                  placeholder="Instruksi khusus untuk pesanan Anda..."></textarea>
              </div>
            </div>

          </form>
        </div>

        <!-- Order Summary -->
        <div class="col-lg-4">
          <div class="order-summary">
            <h4 class="order-summary-title">Ringkasan Pesanan</h4>

            <!-- Cart Items -->
            <div class="order-items">
              @foreach($cartItems as $item)
                <div class="order-item">
                  <div class="order-item-image">
                    @if($item->product->images->where('is_primary', true)->first())
                      <img
                        src="{{ asset('storage/' . $item->product->images->where('is_primary', true)->first()->file_path) }}"
                        alt="{{ $item->product->name }}"
                        onerror="this.src='{{ asset('furni-1.0.0/images/product-1.png') }}';">
                    @else
                      <img src="{{ asset('furni-1.0.0/images/product-1.png') }}" alt="{{ $item->product->name }}">
                    @endif
                  </div>
                  <div class="order-item-details">
                    <h6 class="order-item-name">{{ $item->product->name }}</h6>
                    <p class="order-item-price">
                      {{ $item->formatted_product_price }} × {{ $item->quantity }}
                    </p>
                    <p class="order-item-subtotal">{{ $item->formatted_subtotal }}</p>
                  </div>
                </div>
              @endforeach
            </div>

            <!-- Order Totals -->
            <div class="order-totals">
              <div class="order-total-row">
                <span>Subtotal</span>
                <span id="orderSubtotal">{{ 'Rp ' . number_format($subtotal, 0, ',', '.') }}</span>
              </div>
              <div class="order-total-row" id="shippingCostRow" style="display: none;">
                <span>Ongkos Kirim</span>
                <span id="shippingCost">Rp 0</span>
              </div>
              <div class="order-total-row order-total-final">
                <span><strong>Total</strong></span>
                <span id="orderTotal"><strong>{{ 'Rp ' . number_format($subtotal, 0, ',', '.') }}</strong></span>
              </div>
            </div>

            <!-- Place Order Button -->
            <button type="submit" form="checkoutForm" id="placeOrderBtn" class="btn btn-primary btn-checkout" disabled>
              <i class="fa fa-credit-card me-2"></i>
              Bayar Pesanan
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('scripts')
  <script>
    $(document).ready(function () {
      let subtotal = {{ $subtotal }};
      let selectedShippingCost = 0;
      let selectedShippingService = '';

      // Handle address selection
      $('input[name="address_id"]').on('change', function () {
        if ($(this).is(':checked')) {
          calculateShipping($(this).val());
        }
      });

      // Initialize with default address if selected
      const defaultAddress = $('input[name="address_id"]:checked');
      if (defaultAddress.length > 0) {
        calculateShipping(defaultAddress.val());
      }

      // Calculate shipping costs
      function calculateShipping(addressId) {
        const shippingSection = $('#shippingSection');
        const shippingOptions = $('#shippingOptions');

        shippingSection.show();
        shippingOptions.html(`
              <div class="text-center py-4">
                  <div class="spinner-border text-primary" role="status">
                      <span class="visually-hidden">Loading shipping options...</span>
                  </div>
                  <p class="mt-2 text-muted">Calculating shipping costs...</p>
              </div>
          `);

        $.post('{{ route("checkout.shipping") }}', {
          address_id: addressId,
          _token: $('meta[name="csrf-token"]').attr('content')
        })
          .done(function (response) {
            if (response.success) {
              // Update shipping info banner
              $('#totalWeight').text(response.total_weight);
              $('#destinationCity').text(response.destination_city);
              $('#shippingInfo').show();

              displayShippingOptions(response.shipping_options, response.destination_city);
              $('#shippingDestination').text(`to ${response.destination_city}`);
            } else {
              $('#shippingInfo').hide();
              shippingOptions.html(`
                      <div class="alert alert-warning">
                          <i class="fa fa-exclamation-triangle me-2"></i>
                          ${response.message}
                      </div>
                  `);
              updatePlaceOrderButton();
            }
          })
          .fail(function (xhr) {
            const errorMessage = xhr.responseJSON?.message || 'Failed to calculate shipping cost';
            shippingOptions.html(`
                  <div class="alert alert-danger">
                      <i class="fa fa-exclamation-circle me-2"></i>
                      ${errorMessage}
                  </div>
              `);
            updatePlaceOrderButton();
          });
      }

      // Display shipping options
      function displayShippingOptions(options, destinationCity) {
        let html = '';

        if (options.length === 0) {
          html = `
                  <div class="alert alert-warning">
                      <i class="fa fa-exclamation-triangle me-2"></i>
                      No shipping options available for ${destinationCity}
                  </div>
              `;
        } else {
          options.forEach(function (option, index) {
            const isRecommended = index === 0; // First option is usually the cheapest/recommended
            html += `
                      <div class="shipping-option ${isRecommended ? 'recommended' : ''}">
                          <div class="form-check">
                              <input class="form-check-input" type="radio" name="shipping_service"
                                     id="shipping_${index}" value="${option.courier_code}:${option.service_code}"
                                     data-cost="${option.cost}"
                                     data-service-name="${option.full_service_name}"
                                     ${index === 0 ? 'checked' : ''}>
                              <label class="form-check-label" for="shipping_${index}">
                                  <div class="shipping-details">
                                      <div class="shipping-header">
                                          <div class="courier-info">
                                              <div class="courier-logo">
                                                  <i class="fa fa-truck"></i>
                                                  <span class="courier-name">${option.courier_name}</span>
                                              </div>
                                              <div class="service-name">${option.service_name}</div>
                                              ${isRecommended ? '<span class="badge bg-success recommended-badge">Recommended</span>' : ''}
                                          </div>
                                          <div class="shipping-cost-info">
                                              <span class="shipping-cost">${option.formatted_cost}</span>
                                              <div class="shipping-etd">
                                                  <i class="fa fa-clock me-1"></i>
                                                  ${option.formatted_etd}
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </label>
                          </div>
                      </div>
                  `;
          });
        }

        $('#shippingOptions').html(html);

        // Handle shipping option selection
        $('input[name="shipping_service"]').on('change', function () {
          if ($(this).is(':checked')) {
            selectedShippingCost = parseInt($(this).data('cost'));
            selectedShippingService = $(this).data('service-name');
            updateOrderTotal();
            updatePlaceOrderButton();
          }
        });

        // Auto-select first option
        const firstOption = $('input[name="shipping_service"]:first');
        if (firstOption.length > 0) {
          selectedShippingCost = parseInt(firstOption.data('cost'));
          selectedShippingService = firstOption.data('service-name');
          updateOrderTotal();
          updatePlaceOrderButton();
        }
      }

      // Update order total
      function updateOrderTotal() {
        const total = subtotal + selectedShippingCost;

        $('#shippingCost').text('Rp ' + new Intl.NumberFormat('id-ID').format(selectedShippingCost));
        $('#orderTotal strong').text('Rp ' + new Intl.NumberFormat('id-ID').format(total));

        if (selectedShippingCost > 0) {
          $('#shippingCostRow').show();
        }
      }

      // Update place order button state
      function updatePlaceOrderButton() {
        const addressSelected = $('input[name="address_id"]:checked').length > 0;
        const shippingSelected = $('input[name="shipping_service"]:checked').length > 0;

        $('#placeOrderBtn').prop('disabled', !(addressSelected && shippingSelected));
      }

      // Handle form submission
      $('#checkoutForm').on('submit', function (e) {
        e.preventDefault();

        const addressId = $('input[name="address_id"]:checked').val();
        const shippingService = $('input[name="shipping_service"]:checked').val();
        const notes = $('#notes').val();

        if (!addressId) {
          Notiflix.Notify.warning('Please select a shipping address');
          return;
        }

        if (!shippingService) {
          Notiflix.Notify.warning('Please select a shipping option');
          return;
        }

        // Disable button and show loading
        const submitBtn = $('#placeOrderBtn');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i>Processing...');

        $.post('{{ route("checkout.process") }}', {
          address_id: addressId,
          shipping_service: shippingService,
          shipping_cost: selectedShippingCost,
          notes: notes,
          _token: $('meta[name="csrf-token"]').attr('content')
        })
          .done(function (response) {
            if (response.success) {
              Notiflix.Notify.success(response.message);
              // Redirect to payment page
              window.location.href = response.redirect_url;
            } else {
              Notiflix.Notify.failure(response.message);
              submitBtn.prop('disabled', false).html(originalText);
            }
          })
          .fail(function (xhr) {
            const errorMessage = xhr.responseJSON?.message || 'Failed to process checkout';
            Notiflix.Notify.failure(errorMessage);
            submitBtn.prop('disabled', false).html(originalText);
          });
      });
    });
  </script>
@endsection

@section('styles')
  <style>
    .checkout-section {
      padding: 40px 0;
      background-color: white;
    }

    .section-title {
      font-size: 1.8rem;
      font-weight: 600;
      color: #333;
      margin-bottom: 20px;
      text-align: left;
    }

    .checkout-section-card {
      background: #fafafa;
      border-radius: 4px;
      padding: 15px;
      margin-bottom: 15px;
      border: 1px solid #e9ecef;
    }

    .checkout-section-title {
      font-size: 14px;
      font-weight: 600;
      color: #333;
      margin-bottom: 12px;
      display: flex;
      align-items: center;
    }

    .checkout-section-title i {
      color: #007bff;
      font-size: 12px;
    }

    .address-option {
      border: 1px solid #ddd;
      border-radius: 4px;
      padding: 10px;
      margin-bottom: 8px;
      transition: all 0.2s ease;
    }

    .address-option:hover {
      border-color: #007bff;
      background-color: #f8f9fa;
    }

    .address-option .form-check-input:checked~.form-check-label .address-option {
      border-color: #007bff;
      background-color: #f0f8ff;
    }

    .address-details {
      margin-left: 20px;
    }

    .address-header {
      display: flex;
      align-items: center;
      margin-bottom: 4px;
    }

    .address-header strong {
      font-size: 13px;
    }

    .address-body p {
      font-size: 11px;
      margin-bottom: 2px;
    }

    .shipping-option {
      border: 1px solid #ddd;
      border-radius: 4px;
      padding: 12px;
      margin-bottom: 8px;
      transition: all 0.2s ease;
      background: white;
    }

    .shipping-option:hover {
      border-color: #007bff;
      background-color: #f8f9fa;
    }

    .shipping-option.recommended {
      border-color: #28a745;
      background: #f8fff8;
    }

    .shipping-option .form-check-input:checked~.form-check-label {
      color: #333;
    }

    .shipping-details {
      margin-left: 20px;
      width: 100%;
    }

    .shipping-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      width: 100%;
    }

    .courier-info {
      flex: 1;
    }

    .courier-logo {
      display: flex;
      align-items: center;
      gap: 6px;
      margin-bottom: 3px;
    }

    .courier-logo i {
      color: #007bff;
      font-size: 12px;
    }

    .courier-name {
      font-weight: 600;
      color: #333;
      font-size: 12px;
    }

    .service-name {
      font-size: 11px;
      color: #666;
      margin-bottom: 3px;
    }

    .recommended-badge {
      font-size: 9px;
      padding: 1px 4px;
    }

    .shipping-cost-info {
      text-align: right;
      flex-shrink: 0;
    }

    .shipping-cost {
      display: block;
      font-weight: 600;
      font-size: 12px;
      color: #28a745;
      margin-bottom: 2px;
    }

    .shipping-etd {
      font-size: 10px;
      color: #666;
      display: flex;
      align-items: center;
      justify-content: flex-end;
      gap: 2px;
    }

    .shipping-info-banner {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 10px;
      border-radius: 4px;
      margin-bottom: 10px;
    }

    .shipping-detail {
      display: flex;
      justify-content: space-around;
      flex-wrap: wrap;
      gap: 8px;
    }

    .shipping-detail-item {
      display: flex;
      align-items: center;
      gap: 4px;
      font-size: 11px;
    }

    .shipping-detail-item i {
      font-size: 11px;
      width: 16px;
      text-align: center;
    }

    .order-summary {
      background: #fafafa;
      border-radius: 4px;
      padding: 15px;
      border: 1px solid #e9ecef;
      position: sticky;
      top: 20px;
    }

    .order-summary-title {
      font-size: 14px;
      font-weight: 600;
      color: #333;
      margin-bottom: 12px;
      text-align: left;
    }

    .order-item {
      display: flex;
      align-items: center;
      padding: 8px 0;
      border-bottom: 1px solid #eee;
    }

    .order-item:last-child {
      border-bottom: none;
    }

    .order-item-image {
      width: 40px;
      height: 40px;
      border-radius: 4px;
      overflow: hidden;
      margin-right: 8px;
    }

    .order-item-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .order-item-details {
      flex: 1;
    }

    .order-item-name {
      font-size: 11px;
      font-weight: 500;
      color: #333;
      margin-bottom: 2px;
      line-height: 1.2;
    }

    .order-item-price {
      font-size: 10px;
      color: #666;
      margin-bottom: 2px;
    }

    .order-item-subtotal {
      font-size: 11px;
      font-weight: 600;
      color: #28a745;
      margin-bottom: 0;
    }

    .order-totals {
      margin-top: 10px;
      padding-top: 10px;
      border-top: 1px solid #ddd;
    }

    .order-total-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 5px;
      font-size: 12px;
    }

    .order-total-final {
      font-size: 13px;
      margin-top: 8px;
      padding-top: 8px;
      border-top: 1px solid #ddd;
    }

    .btn-checkout {
      width: 100%;
      padding: 8px 12px;
      font-size: 12px;
      font-weight: 600;
      margin-top: 10px;
      border-radius: 4px;
      text-transform: none;
      letter-spacing: normal;
    }

    .btn-checkout:disabled {
      opacity: 0.6;
      cursor: not-allowed;
    }

    .form-control {
      font-size: 12px;
      padding: 6px 8px;
      border-radius: 4px;
    }

    .badge {
      font-size: 9px;
      padding: 2px 4px;
    }

    @media (max-width: 768px) {
      .checkout-section {
        padding: 40px 0;
      }

      .checkout-section-card {
        padding: 1.5rem;
      }

      .order-summary {
        margin-top: 2rem;
        position: static;
      }

      .section-title {
        font-size: 2rem;
      }
    }
  </style>
@endsection