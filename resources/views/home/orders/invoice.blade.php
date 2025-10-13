<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $order->order_number }}</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: white;
            color: #333;
            line-height: 1.4;
            font-size: 12px;
        }

        .invoice-container {
            max-width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 20mm;
            background: white;
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }

        .company-info h1 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .company-info p {
            font-size: 12px;
            color: #666;
            margin: 2px 0;
        }

        .invoice-title {
            text-align: right;
        }

        .invoice-title h2 {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .invoice-title p {
            font-size: 12px;
            color: #666;
            margin: 2px 0;
        }

        .invoice-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 30px;
        }

        .detail-section h3 {
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
        }

        .detail-section p {
            font-size: 12px;
            margin: 3px 0;
            color: #666;
        }

        .detail-section strong {
            color: #333;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .items-table th {
            background: #f8f9fa;
            padding: 12px 8px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            border-bottom: 2px solid #ddd;
        }

        .items-table td {
            padding: 12px 8px;
            border-bottom: 1px solid #eee;
            font-size: 12px;
        }

        .items-table .text-center {
            text-align: center;
        }

        .items-table .text-right {
            text-align: right;
        }

        .product-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .product-image {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 4px;
        }

        .product-details h4 {
            font-size: 12px;
            font-weight: 500;
            margin-bottom: 2px;
        }

        .product-details small {
            font-size: 12px;
            color: #888;
        }

        .totals-section {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 40px;
            margin-bottom: 30px;
        }

        .totals {
            min-width: 250px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 12px;
        }

        .total-row.grand-total {
            border-top: 2px solid #ddd;
            font-size: 12px;
            font-weight: 600;
            padding-top: 12px;
            margin-top: 8px;
        }

        .notes {
            margin-bottom: 20px;
        }

        .notes h4 {
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .notes p {
            font-size: 12px;
            color: #666;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #888;
        }

        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #333;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-success { background: #d4edda; color: #155724; }
        .status-info { background: #d1ecf1; color: #0c5460; }
        .status-warning { background: #fff3cd; color: #856404; }

        @media print {
            body {
                background: white !important;
                -webkit-print-color-adjust: exact;
            }

            .print-btn {
                display: none !important;
            }

            .invoice-container {
                max-width: none;
                margin: 0;
                padding: 15mm;
                min-height: auto;
            }

            @page {
                size: A4;
                margin: 0;
            }
        }

        @media screen and (max-width: 768px) {
            .invoice-container {
                max-width: 100%;
                padding: 20px;
                margin: 0;
            }

            .invoice-header {
                flex-direction: column;
                gap: 20px;
            }

            .invoice-title {
                text-align: left;
            }

            .invoice-details {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .items-table th,
            .items-table td {
                padding: 8px 4px;
            }

            .product-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }

            .product-image {
                width: 30px;
                height: 30px;
            }

            .totals-section {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .totals {
                min-width: auto;
            }
        }

        @media screen and (max-width: 480px) {
            .invoice-container {
                padding: 15px;
            }

            .items-table th,
            .items-table td {
                padding: 6px 3px;
            }
        }
    </style>
</head>
<body>
    <!-- Print Button -->
    <button class="btn btn-primary print-btn no-print" onclick="window.print()">
        <i class="fa fa-print me-2"></i>Print Invoice
    </button>

    <div class="container mt-4">
        <div class="invoice-container">
            <!-- Invoice Header -->
            <div class="invoice-header">
                <div class="row">
                    <div class="col-md-6">
                        <div class="company-info">
                            <h1>{{ $systemSettings['app_name'] ?? config('app.name', 'Marketplace') }}</h1>
                            <p class="text-muted mb-1">{{ $shippingAddress['full_address'] ?? 'Jl. Contoh Alamat No. 123, Jakarta' }}</p>
                            <p class="text-muted mb-1">Phone: {{ $systemSettings['contact_phone'] ?? '+62 812 3456 7890' }}</p>
                            <p class="text-muted mb-0">Email: {{ $systemSettings['contact_email'] ?? 'info@marketplace.com' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <h2 class="invoice-title">INVOICE</h2>
                        <p class="text-muted mb-0">Invoice Date: {{ $order->created_at->format('d M Y') }}</p>
                        @if($order->paid_at)
                            <p class="text-muted mb-0">Payment Date: {{ $order->paid_at->format('d M Y') }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Invoice Meta Information -->
            <div class="invoice-meta">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="fw-bold text-primary mb-3">Invoice To:</h5>
                        <p class="mb-1"><strong>{{ $order->shipping_address['recipient_name'] ?? $order->user->name }}</strong></p>
                        <p class="mb-1">{{ $order->user->email }}</p>
                        @if(isset($order->shipping_address['phone']))
                            <p class="mb-1">Phone: {{ $order->shipping_address['phone'] }}</p>
                        @endif
                        <div class="mt-2">
                            <strong>Shipping Address:</strong><br>
                            {{ $order->shipping_address['address'] ?? '' }}<br>
                            {{ $order->shipping_address['village_name'] ?? '' }}, {{ $order->shipping_address['district_name'] ?? '' }}<br>
                            {{ $order->shipping_address['city_name'] ?? '' }}, {{ $order->shipping_address['province_name'] ?? '' }}<br>
                            {{ $order->shipping_address['postal_code'] ?? '' }}
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <h5 class="fw-bold text-primary mb-3">Invoice Details:</h5>
                        <p class="mb-1"><strong>Invoice Number:</strong> INV-{{ $order->order_number }}</p>
                        <p class="mb-1"><strong>Order Number:</strong> {{ $order->order_number }}</p>
                        <p class="mb-1"><strong>Payment Method:</strong> {{ ucfirst($order->payment_method ?? 'Midtrans') }}</p>
                        <p class="mb-1"><strong>Status:</strong>
                            <span class="badge bg-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'paid' ? 'info' : 'warning') }} status-badge">
                                {{ ucfirst($order->status) }}
                            </span>
                        </p>
                        @if($order->shipping_service)
                            <p class="mb-1"><strong>Shipping:</strong> {{ $order->shipping_service }}</p>
                        @endif
                        @if($order->tracking_number)
                            <p class="mb-0"><strong>Tracking:</strong> {{ $order->tracking_number }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="table-responsive mb-4">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="width: 5%">#</th>
                            <th style="width: 45%">Product</th>
                            <th style="width: 15%" class="text-center">Quantity</th>
                            <th style="width: 15%" class="text-end">Unit Price</th>
                            <th style="width: 20%" class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($item->product_image)
                                            <img src="{{ asset('storage/' . $item->product_image) }}"
                                                 alt="{{ $item->product_name }}"
                                                 class="me-3"
                                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                        @endif
                                        <div>
                                            <h6 class="mb-1">{{ $item->product_name }}</h6>
                                            @if($item->product_sku)
                                                <small class="text-muted">SKU: {{ $item->product_sku }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-end">{{ $item->formatted_product_price }}</td>
                                <td class="text-end fw-bold">{{ $item->formatted_subtotal }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Totals Section -->
            <div class="row">
                <div class="col-md-6">
                    @if($order->notes)
                        <div class="mt-3">
                            <h6 class="fw-bold">Order Notes:</h6>
                            <p class="text-muted">{{ $order->notes }}</p>
                        </div>
                    @endif
                </div>
                <div class="col-md-6">
                    <div class="total-section">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>{{ $order->formatted_subtotal }}</span>
                        </div>
                        @if($order->shipping_cost > 0)
                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping Cost:</span>
                                <span>{{ $order->formatted_shipping_cost }}</span>
                            </div>
                        @endif
                        @if($order->tax_amount > 0)
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax:</span>
                                <span>Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <hr>
                        <div class="d-flex justify-content-between grand-total">
                            <span>Grand Total:</span>
                            <span>{{ $order->formatted_total_amount }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-5 pt-4 border-top">
                <p class="text-muted mb-0">Thank you for your business!</p>
                <small class="text-muted">This is a computer-generated invoice and does not require a signature.</small>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and Font Awesome -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>

    <script>
        // Auto-focus for printing
        window.onload = function() {
            // Add print functionality
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey && e.key === 'p') {
                    e.preventDefault();
                    window.print();
                }
            });
        };
    </script>
</body>
</html>