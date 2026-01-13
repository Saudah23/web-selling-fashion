@extends('layouts.app')

@section('title', 'Manajemen Pesanan - Dashboard Admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">
                        <i class="fas fa-shopping-cart me-2"></i>Manajemen Pesanan
                    </div>
                    <div class="card-tools">
                        <button class="btn btn-warning btn-round btn-sm me-2" onclick="showBulkUpdateModal()">
                            <i class="fas fa-edit me-2"></i>Update Status Massal
                        </button>
                        <button class="btn btn-info btn-round btn-sm me-2" onclick="showOrderStatistics()">
                            <i class="fas fa-chart-bar me-2"></i>Statistik
                        </button>
                        <button class="btn btn-success btn-round btn-sm" onclick="refreshOrders()">
                            <i class="fas fa-sync-alt me-2"></i>Muat Ulang
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="jsGrid"></div>
                <div id="pager" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>

<!-- Order Detail Modal -->
<div class="modal fade" id="orderDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Pesanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="orderDetailContent">
                <!-- Dynamic content -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="editOrderStatusBtn" onclick="showStatusUpdateModal()">
                    <i class="fas fa-edit me-2"></i>Perbarui Status
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusUpdateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Perbarui Status Pesanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="statusUpdateForm">
                <div class="modal-body">
                    <input type="hidden" id="updateOrderId">

                    <div class="mb-3">
                        <label for="updateStatus" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="updateStatus" name="status" required>
                            <option value="">-- Pilih Status --</option>
                            <option value="pending">Menunggu</option>
                            <option value="paid">Dibayar</option>
                            <option value="processing">Diproses</option>
                            <option value="shipped">Dikirim</option>
                            <option value="delivered">Terkirim</option>
                            <option value="cancelled">Dibatalkan</option>
                            <option value="refunded">Dikembalikan</option>
                        </select>
                    </div>

                    <div class="mb-3" id="trackingNumberField" style="display: none;">
                        <label for="updateTrackingNumber" class="form-label">Nomor Resi</label>
                        <input type="text" class="form-control" id="updateTrackingNumber" name="tracking_number" placeholder="Masukkan nomor resi">
                    </div>

                    <div class="mb-3">
                        <label for="updateNotes" class="form-label">Catatan</label>
                        <textarea class="form-control" id="updateNotes" name="notes" rows="3" placeholder="Tambahkan catatan tentang pembaruan status ini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Perbarui Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Quick Shipping Modal -->
<div class="modal fade" id="shippingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tandai Pesanan Dikirim</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="shippingForm">
                <div class="modal-body">
                    <input type="hidden" id="shippingOrderId">

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Informasi Pengiriman Diperlukan</strong><br>
                        Silakan berikan nomor resi agar pelanggan dapat melacak pesanan mereka.
                    </div>

                    <div class="mb-3">
                        <label for="shippingTrackingNumber" class="form-label">Nomor Resi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="shippingTrackingNumber" name="tracking_number" placeholder="Masukkan nomor resi" required>
                        <div class="form-text">Pelanggan akan menerima nomor resi ini via email/SMS</div>
                    </div>

                    <div class="mb-3">
                        <label for="shippingNotes" class="form-label">Catatan Pengiriman</label>
                        <textarea class="form-control" id="shippingNotes" name="notes" rows="2" placeholder="Catatan pengiriman opsional..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-truck me-2"></i>Tandai Dikirim
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Update Modal -->
<div class="modal fade" id="bulkUpdateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Status Massal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Pilih pesanan dan perbarui statusnya secara massal. Harap gunakan fitur ini dengan hati-hati.
                </div>
                <div id="bulkUpdateContent">
                    <div class="text-center py-4">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat pesanan...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="updateBulkStatusBtn">Perbarui Status</button>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Modal -->
<div class="modal fade" id="statisticsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Statistik Pesanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="statisticsContent">
                <!-- Dynamic content -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('notiflix-Notiflix-67ba12d/dist/notiflix-3.2.8.min.css') }}">
<style>
/* Order specific styles */
#orderDetailModal .modal-body {
    max-height: 70vh;
    overflow-y: auto;
}

.form-control.is-invalid {
    border-color: #dc3545;
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
}

/* JSGrid table styling */
#jsGrid .jsgrid-table {
    font-size: 12px;
}

#jsGrid .jsgrid-header-row > .jsgrid-header-cell,
#jsGrid .jsgrid-filter-row > .jsgrid-cell,
#jsGrid .jsgrid-row > .jsgrid-cell {
    padding: 8px 6px;
    vertical-align: middle;
    text-align: center;
    border-right: 1px solid #dee2e6;
}

#jsGrid .jsgrid-header-row > .jsgrid-header-cell {
    background-color: #f8f9fa;
    font-weight: 600;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Column specific alignment */
#jsGrid .price-column {
    text-align: right !important;
}

#jsGrid .text-center {
    text-align: center !important;
}

#jsGrid .text-end {
    text-align: right !important;
}

/* Responsive table adjustments */
#jsGrid .jsgrid-cell {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Filter input styling */
#jsGrid .jsgrid-filter-row input,
#jsGrid .jsgrid-filter-row select {
    font-size: 11px;
    padding: 4px 6px;
    height: auto;
}

/* Button styling in table */
#jsGrid .btn-sm {
    padding: 2px 6px;
    font-size: 10px;
}

#jsGrid .btn-group-sm .btn {
    margin: 0 1px;
}

.order-status-pending {
    color: #ffc107;
}

.order-status-paid {
    color: #17a2b8;
}

.order-status-processing {
    color: #007bff;
}

.order-status-shipped {
    color: #6c757d;
}

.order-status-delivered {
    color: #28a745;
}

.order-status-cancelled {
    color: #dc3545;
}

.order-status-refunded {
    color: #343a40;
}

.bulk-order-item {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 1rem;
    margin-bottom: 1rem;
}

.bulk-order-item:last-child {
    margin-bottom: 0;
}

.bulk-order-item.selected {
    border-color: #0d6efd;
    background-color: #f8f9fa;
}

.order-timeline {
    position: relative;
    padding-left: 1.5rem;
}

.order-timeline::before {
    content: '';
    position: absolute;
    left: 0.5rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 1.5rem;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -0.75rem;
    top: 0.25rem;
    width: 1rem;
    height: 1rem;
    border-radius: 50%;
    background: #fff;
    border: 2px solid #dee2e6;
}

.timeline-item.completed::before {
    background: #28a745;
    border-color: #28a745;
}

.timeline-item.current::before {
    background: #007bff;
    border-color: #007bff;
}

.order-item-card {
    border: 1px solid #dee2e6;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 1rem;
}

.product-image {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 0.375rem;
}
</style>
@endpush

@push('scripts')
<script src="{{ asset('notiflix-Notiflix-67ba12d/dist/notiflix-aio-3.2.8.min.js') }}"></script>
<script>
let orderDetailModal;
let statusUpdateModal;
let shippingModal;
let bulkUpdateModal;
let statisticsModal;
let currentOrderId = null;

$(document).ready(function() {
    // Initialize modals
    orderDetailModal = new bootstrap.Modal(document.getElementById('orderDetailModal'));
    statusUpdateModal = new bootstrap.Modal(document.getElementById('statusUpdateModal'));
    shippingModal = new bootstrap.Modal(document.getElementById('shippingModal'));
    bulkUpdateModal = new bootstrap.Modal(document.getElementById('bulkUpdateModal'));
    statisticsModal = new bootstrap.Modal(document.getElementById('statisticsModal'));

    // Configure Notiflix
    Notiflix.Notify.init({
        width: '300px',
        position: 'right-top',
        distance: '20px',
        opacity: 1,
        timeout: 3000,
    });

    // Initialize JSGrid with responsive configuration
    $("#jsGrid").jsGrid({
        height: "auto",
        width: "100%",

        filtering: true,
        editing: false,
        sorting: true,
        paging: true,
        pageSize: 15,
        pageButtonCount: 5,

        autoload: true,
        controller: {
            loadData: function(filter) {
                return $.ajax({
                    type: "GET",
                    url: "{{ route('admin.orders.data') }}",
                    data: filter,
                    dataType: "json"
                });
            }
        },

        fields: [
            {
                name: "order_number",
                title: "Nomor Pesanan",
                type: "text",
                width: 180,
                filtering: true,
                css: "order-number-column text-center",
                headerCss: "text-center",
                itemTemplate: function(value) {
                    return '<code class="text-primary small">' + value + '</code>';
                }
            },
            {
                name: "customer_name",
                title: "Pelanggan",
                type: "text",
                width: 140,
                filtering: false,
                css: "customer-column text-center",
                headerCss: "text-center",
                itemTemplate: function(value) {
                    return '<span class="small">' + value + '</span>';
                }
            },
            {
                name: "items_count",
                title: "Item",
                type: "number",
                width: 70,
                filtering: false,
                css: "items-column text-center",
                headerCss: "text-center",
                itemTemplate: function(value) {
                    return '<span class="badge bg-light text-dark small">' + value + '</span>';
                }
            },
            {
                name: "total_amount",
                title: "Total",
                type: "text",
                width: 110,
                filtering: false,
                css: "price-column text-center",
                headerCss: "text-center",
                itemTemplate: function(value) {
                    return '<strong class="text-success small">' + value + '</strong>';
                }
            },
            {
                name: "status",
                title: "Status",
                type: "select",
                width: 100,
                filtering: true,
                css: "status-column text-center",
                headerCss: "text-center",
                items: [
                    { Name: "", Id: "" },
                    { Name: "Menunggu", Id: "pending" },
                    { Name: "Dibayar", Id: "paid" },
                    { Name: "Diproses", Id: "processing" },
                    { Name: "Dikirim", Id: "shipped" },
                    { Name: "Terkirim", Id: "delivered" },
                    { Name: "Dibatalkan", Id: "cancelled" },
                    { Name: "Dikembalikan", Id: "refunded" }
                ],
                valueField: "Id",
                textField: "Name",
                itemTemplate: function(value, item) {
                    return item.status_badge;
                }
            },
            {
                name: "tracking_number",
                title: "Resi",
                type: "text",
                width: 180,
                filtering: true,
                css: "tracking-column text-center",
                headerCss: "text-center",
                itemTemplate: function(value) {
                    return value && value !== 'N/A' ?
                        '<code class="text-info small">' + value + '</code>' :
                        '<span class="text-muted small">-</span>';
                }
            },
            {
                name: "created_date",
                title: "Tanggal",
                type: "text",
                width: 140,
                filtering: true,
                css: "date-column text-center",
                headerCss: "text-center",
                itemTemplate: function(value, item) {
                    return '<span class="small">' + item.created_at + '</span>';
                }
            },
            {
                type: "control",
                title: "Aksi",
                width: 80,
                css: "actions-column text-center",
                headerCss: "text-center",
                itemTemplate: function(value, item) {
                    let actions = '<div class="btn-group btn-group-sm" role="group">' +
                                 '<button type="button" class="btn btn-outline-info btn-sm" onclick="viewOrder(' + item.id + ')" title="Lihat Detail">' +
                                 '<i class="fas fa-eye"></i>' +
                                 '</button>';

                    // One-click status update buttons based on current status
                    if (item.status === 'paid') {
                        actions += '<button type="button" class="btn btn-outline-primary btn-sm" onclick="quickUpdateStatus(' + item.id + ', \'processing\')" title="Tandai Diproses">' +
                                   '<i class="fas fa-cogs"></i>' +
                                   '</button>';
                    } else if (item.status === 'processing') {
                        actions += '<button type="button" class="btn btn-outline-secondary btn-sm" onclick="showShippingModal(' + item.id + ')" title="Tandai Dikirim">' +
                                   '<i class="fas fa-truck"></i>' +
                                   '</button>';
                    } else if (item.status === 'shipped') {
                        actions += '<button type="button" class="btn btn-outline-info btn-sm disabled" title="Status terkirim hanya dapat diperbarui oleh pelanggan">' +
                                   '<i class="fas fa-info-circle"></i>' +
                                   '</button>';
                    } else {
                        actions += '<button type="button" class="btn btn-outline-warning btn-sm" onclick="showStatusUpdateModal(' + item.id + ')" title="Perbarui Status">' +
                                   '<i class="fas fa-edit"></i>' +
                                   '</button>';
                    }

                    actions += '</div>';
                    return actions;
                }
            }
        ]
    });

    // Form submissions
    $('#statusUpdateForm').on('submit', function(e) {
        e.preventDefault();
        submitStatusUpdate();
    });

    $('#shippingForm').on('submit', function(e) {
        e.preventDefault();
        submitShippingUpdate();
    });

    // Show/hide tracking number field based on status
    $('#updateStatus').on('change', function() {
        const status = $(this).val();
        if (status === 'shipped') {
            $('#trackingNumberField').show();
        } else {
            $('#trackingNumberField').hide();
        }
    });

    // Bulk update
    $('#updateBulkStatusBtn').on('click', function() {
        updateBulkStatus();
    });
});

function viewOrder(id) {
    currentOrderId = id;

    $.ajax({
        url: `/admin/orders/${id}`,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                renderOrderDetails(response.data);
                orderDetailModal.show();
            } else {
                Notiflix.Notify.failure('Failed to load order details');
            }
        },
        error: function() {
            Notiflix.Notify.failure('Failed to load order details');
        }
    });
}

function renderOrderDetails(order) {
    const shippingAddress = order.shipping_address;

    let content = `
        <div class="row mb-4">
            <div class="col-md-6">
                <h6><i class="fas fa-info-circle me-2"></i>Order Information</h6>
                <table class="table table-sm">
                    <tr><td><strong>Order Number:</strong></td><td><code>${order.order_number}</code></td></tr>
                    <tr><td><strong>Status:</strong></td><td>${getStatusBadge(order.status)}</td></tr>
                    <tr><td><strong>Created:</strong></td><td>${formatDateTime(order.created_at)}</td></tr>
                    ${order.paid_at ? `<tr><td><strong>Paid:</strong></td><td>${formatDateTime(order.paid_at)}</td></tr>` : ''}
                    ${order.shipped_at ? `<tr><td><strong>Shipped:</strong></td><td>${formatDateTime(order.shipped_at)}</td></tr>` : ''}
                    ${order.delivered_at ? `<tr><td><strong>Delivered:</strong></td><td>${formatDateTime(order.delivered_at)}</td></tr>` : ''}
                </table>
            </div>
            <div class="col-md-6">
                <h6><i class="fas fa-user me-2"></i>Customer Information</h6>
                <table class="table table-sm">
                    <tr><td><strong>Name:</strong></td><td>${order.user.name}</td></tr>
                    <tr><td><strong>Email:</strong></td><td>${order.user.email}</td></tr>
                    <tr><td><strong>Phone:</strong></td><td>${order.user.phone || 'N/A'}</td></tr>
                </table>

                <h6><i class="fas fa-truck me-2"></i>Shipping Information</h6>
                <table class="table table-sm">
                    <tr><td><strong>Service:</strong></td><td>${order.shipping_service || 'N/A'}</td></tr>
                    <tr><td><strong>Courier:</strong></td><td>${order.shipping_courier || 'N/A'}</td></tr>
                    <tr><td><strong>ETD:</strong></td><td>${order.shipping_etd || 'N/A'}</td></tr>
                    <tr><td><strong>Tracking:</strong></td><td>${order.tracking_number ? '<code>' + order.tracking_number + '</code>' : 'N/A'}</td></tr>
                </table>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h6><i class="fas fa-map-marker-alt me-2"></i>Shipping Address</h6>
                <div class="border p-3 rounded bg-light">
                    <strong>${shippingAddress.recipient_name}</strong><br>
                    ${shippingAddress.phone}<br>
                    ${shippingAddress.address}<br>
                    ${shippingAddress.city_name}, ${shippingAddress.province_name}
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h6><i class="fas fa-shopping-bag me-2"></i>Order Items</h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Price</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
    `;

    order.items.forEach(item => {
        content += `
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        ${item.product_image ?
                            `<img src="/${item.product_image}" class="product-image me-3" alt="${item.product_name}">` :
                            '<div class="product-image me-3 bg-light d-flex align-items-center justify-content-center"><i class="fas fa-image text-muted"></i></div>'
                        }
                        <div>
                            <strong>${item.product_name}</strong>
                            ${item.product_attributes ? '<br><small class="text-muted">' + JSON.stringify(item.product_attributes) + '</small>' : ''}
                        </div>
                    </div>
                </td>
                <td><code class="small">${item.product_sku || 'N/A'}</code></td>
                <td class="text-center">${item.quantity}</td>
                <td class="text-end">${item.formatted_product_price}</td>
                <td class="text-end">${item.formatted_subtotal}</td>
            </tr>
        `;
    });

    content += `
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="4" class="text-end">Subtotal:</th>
                                <th class="text-end">${order.formatted_subtotal}</th>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-end">Shipping:</th>
                                <th class="text-end">${order.formatted_shipping_cost}</th>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-end">Tax:</th>
                                <th class="text-end">Rp ${Number(order.tax_amount).toLocaleString('id-ID')}</th>
                            </tr>
                            <tr class="table-primary">
                                <th colspan="4" class="text-end">Total:</th>
                                <th class="text-end">${order.formatted_total_amount}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    `;

    if (order.notes) {
        content += `
            <div class="row">
                <div class="col-12">
                    <h6><i class="fas fa-sticky-note me-2"></i>Notes</h6>
                    <div class="border p-3 rounded bg-light">
                        ${order.notes}
                    </div>
                </div>
            </div>
        `;
    }

    $('#orderDetailContent').html(content);
}

function showStatusUpdateModal(orderId = null) {
    if (orderId) {
        currentOrderId = orderId;
    }

    if (!currentOrderId) {
        Notiflix.Notify.warning('Please select an order first');
        return;
    }

    // Reset form
    $('#statusUpdateForm')[0].reset();
    $('#updateOrderId').val(currentOrderId);
    $('#trackingNumberField').hide();

    // If modal was opened from detail modal, hide it first
    if (orderId) {
        statusUpdateModal.show();
    } else {
        orderDetailModal.hide();
        setTimeout(() => {
            statusUpdateModal.show();
        }, 300);
    }
}

function submitStatusUpdate() {
    const formData = {
        status: $('#updateStatus').val(),
        tracking_number: $('#updateTrackingNumber').val(),
        notes: $('#updateNotes').val()
    };

    if (!formData.status) {
        Notiflix.Notify.warning('Please select a status');
        return;
    }

    Notiflix.Loading.circle('Updating status...');

    $.ajax({
        url: `/admin/orders/${currentOrderId}/status`,
        type: 'POST',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            Notiflix.Loading.remove();

            if (response.success) {
                Notiflix.Notify.success(response.message);
                statusUpdateModal.hide();
                $("#jsGrid").jsGrid("loadData");

                // Refresh detail modal if it was open
                if (currentOrderId) {
                    setTimeout(() => {
                        viewOrder(currentOrderId);
                    }, 500);
                }
            } else {
                Notiflix.Notify.failure(response.message);
            }
        },
        error: function(xhr) {
            Notiflix.Loading.remove();
            const response = xhr.responseJSON;
            Notiflix.Notify.failure(response?.message || 'Failed to update status');
        }
    });
}

function showBulkUpdateModal() {
    $('#bulkUpdateContent').html(`
        <div class="text-center py-4">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading orders...</p>
        </div>
    `);

    bulkUpdateModal.show();

    // Load orders for bulk update
    $.ajax({
        url: "{{ route('admin.orders.data') }}",
        type: 'GET',
        success: function(orders) {
            let content = `
                <form id="bulkUpdateForm">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">New Status</label>
                            <select class="form-select" id="bulkStatus" required>
                                <option value="">-- Select Status --</option>
                                <option value="paid">Paid</option>
                                <option value="processing">Processing</option>
                                <option value="shipped">Shipped</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Notes (Optional)</label>
                            <input type="text" class="form-control" id="bulkNotes" placeholder="Bulk update notes">
                        </div>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="selectAllOrders">
                            <label class="form-check-label" for="selectAllOrders">
                                <strong>Select All Orders</strong>
                            </label>
                        </div>
                    </div>
            `;

            if (orders.length === 0) {
                content += '<div class="alert alert-warning">No orders found.</div>';
            } else {
                orders.forEach(function(order) {
                    content += `
                        <div class="bulk-order-item" data-order-id="${order.id}">
                            <div class="row align-items-center">
                                <div class="col-md-1">
                                    <div class="form-check">
                                        <input class="form-check-input bulk-select" type="checkbox" value="${order.id}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <strong>${order.order_number}</strong><br>
                                    <small class="text-muted">${order.customer_name}</small>
                                </div>
                                <div class="col-md-2">
                                    ${order.status_badge}
                                </div>
                                <div class="col-md-2">
                                    <strong>${order.total_amount}</strong>
                                </div>
                                <div class="col-md-2">
                                    <small>${order.created_at}</small>
                                </div>
                                <div class="col-md-2" id="trackingField-${order.id}" style="display: none;">
                                    <input type="text" class="form-control form-control-sm tracking-input"
                                           placeholder="Tracking number" disabled>
                                </div>
                            </div>
                        </div>
                    `;
                });
            }

            content += '</form>';
            $('#bulkUpdateContent').html(content);

            // Enable/disable tracking inputs and selection
            $('.bulk-select').on('change', function() {
                const orderItem = $(this).closest('.bulk-order-item');
                const orderId = $(this).val();
                const trackingField = $(`#trackingField-${orderId}`);
                const trackingInput = trackingField.find('.tracking-input');

                if ($(this).is(':checked')) {
                    orderItem.addClass('selected');
                    if ($('#bulkStatus').val() === 'shipped') {
                        trackingField.show();
                        trackingInput.prop('disabled', false);
                    }
                } else {
                    orderItem.removeClass('selected');
                    trackingField.hide();
                    trackingInput.prop('disabled', true);
                }
            });

            // Select all functionality
            $('#selectAllOrders').on('change', function() {
                $('.bulk-select').prop('checked', $(this).is(':checked')).trigger('change');
            });

            // Show/hide tracking fields based on status
            $('#bulkStatus').on('change', function() {
                const status = $(this).val();
                $('.bulk-select:checked').each(function() {
                    const orderId = $(this).val();
                    const trackingField = $(`#trackingField-${orderId}`);
                    const trackingInput = trackingField.find('.tracking-input');

                    if (status === 'shipped') {
                        trackingField.show();
                        trackingInput.prop('disabled', false);
                    } else {
                        trackingField.hide();
                        trackingInput.prop('disabled', true);
                    }
                });
            });
        },
        error: function() {
            $('#bulkUpdateContent').html('<div class="alert alert-danger">Failed to load orders.</div>');
        }
    });
}

function updateBulkStatus() {
    const selectedOrders = [];
    const trackingNumbers = [];

    $('.bulk-select:checked').each(function() {
        const orderId = $(this).val();
        const trackingInput = $(`#trackingField-${orderId} .tracking-input`);

        selectedOrders.push(orderId);
        trackingNumbers.push(trackingInput.val() || '');
    });

    if (selectedOrders.length === 0) {
        Notiflix.Notify.warning('Please select at least one order to update.');
        return;
    }

    const status = $('#bulkStatus').val();
    if (!status) {
        Notiflix.Notify.warning('Please select a status to update.');
        return;
    }

    Notiflix.Loading.circle('Updating orders...');

    $.ajax({
        url: "{{ route('admin.orders.bulk-update-status') }}",
        type: 'POST',
        data: {
            order_ids: selectedOrders,
            status: status,
            tracking_numbers: trackingNumbers,
            notes: $('#bulkNotes').val()
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            Notiflix.Loading.remove();

            if (response.success) {
                Notiflix.Notify.success(response.message);
                bulkUpdateModal.hide();
                $("#jsGrid").jsGrid("loadData");
            } else {
                Notiflix.Notify.failure(response.message);
            }
        },
        error: function(xhr) {
            Notiflix.Loading.remove();
            const response = xhr.responseJSON;
            Notiflix.Notify.failure(response?.message || 'Failed to update orders');
        }
    });
}

function showOrderStatistics() {
    $('#statisticsContent').html(`
        <div class="text-center py-4">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading statistics...</p>
        </div>
    `);

    statisticsModal.show();

    $.ajax({
        url: "{{ route('admin.orders.statistics') }}",
        type: 'GET',
        success: function(response) {
            if (response.success) {
                renderStatistics(response.data);
            } else {
                $('#statisticsContent').html('<div class="alert alert-danger">Failed to load statistics.</div>');
            }
        },
        error: function() {
            $('#statisticsContent').html('<div class="alert alert-danger">Failed to load statistics.</div>');
        }
    });
}

function renderStatistics(stats) {
    let content = `
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card border-primary">
                    <div class="card-body text-center">
                        <h5 class="card-title text-primary">${stats.total_orders}</h5>
                        <p class="card-text">Total Orders</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <h5 class="card-title text-success">${stats.formatted_total_revenue}</h5>
                        <p class="card-text">Total Revenue</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card border-info">
                    <div class="card-body text-center">
                        <h5 class="card-title text-info">${stats.today_orders}</h5>
                        <p class="card-text">Today's Orders</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card border-warning">
                    <div class="card-body text-center">
                        <h5 class="card-title text-warning">${stats.this_month_orders}</h5>
                        <p class="card-text">This Month</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h6>Orders by Status</h6>
                <div class="row">
                    <div class="col-md-2 col-sm-4 col-6 mb-2">
                        <div class="text-center">
                            <div class="h5 text-warning">${stats.pending_orders}</div>
                            <small>Pending</small>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-4 col-6 mb-2">
                        <div class="text-center">
                            <div class="h5 text-info">${stats.paid_orders}</div>
                            <small>Paid</small>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-4 col-6 mb-2">
                        <div class="text-center">
                            <div class="h5 text-primary">${stats.processing_orders}</div>
                            <small>Processing</small>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-4 col-6 mb-2">
                        <div class="text-center">
                            <div class="h5 text-secondary">${stats.shipped_orders}</div>
                            <small>Shipped</small>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-4 col-6 mb-2">
                        <div class="text-center">
                            <div class="h5 text-success">${stats.delivered_orders}</div>
                            <small>Delivered</small>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-4 col-6 mb-2">
                        <div class="text-center">
                            <div class="h5 text-danger">${stats.cancelled_orders}</div>
                            <small>Cancelled</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <h6>Recent Orders</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
    `;

    stats.recent_orders.forEach(order => {
        content += `
            <tr>
                <td><code>${order.order_number}</code></td>
                <td>${order.customer_name}</td>
                <td>${order.total_amount}</td>
                <td>${order.status_badge}</td>
                <td><small>${order.created_at}</small></td>
            </tr>
        `;
    });

    content += `
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    `;

    $('#statisticsContent').html(content);
}

// Quick status update functions
function quickUpdateStatus(orderId, newStatus) {
    const statusLabels = {
        'processing': 'Processing',
        'delivered': 'Delivered'
    };

    const confirmMessage = `Are you sure you want to mark this order as ${statusLabels[newStatus]}?`;

    Notiflix.Confirm.show(
        'Confirm Status Update',
        confirmMessage,
        'Yes, Update',
        'Cancel',
        function() {
            Notiflix.Loading.circle('Updating status...');

            $.ajax({
                url: `/admin/orders/${orderId}/status`,
                type: 'POST',
                data: {
                    status: newStatus,
                    notes: `Quick update to ${statusLabels[newStatus]} by admin`
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Notiflix.Loading.remove();

                    if (response.success) {
                        Notiflix.Notify.success(response.message);
                        $("#jsGrid").jsGrid("loadData");
                    } else {
                        Notiflix.Notify.failure(response.message);
                    }
                },
                error: function(xhr) {
                    Notiflix.Loading.remove();
                    const response = xhr.responseJSON;
                    Notiflix.Notify.failure(response?.message || 'Failed to update status');
                }
            });
        }
    );
}

function showShippingModal(orderId) {
    currentOrderId = orderId;

    // Reset form
    $('#shippingForm')[0].reset();
    $('#shippingOrderId').val(orderId);

    shippingModal.show();
}

function submitShippingUpdate() {
    const trackingNumber = $('#shippingTrackingNumber').val().trim();
    const notes = $('#shippingNotes').val().trim();

    if (!trackingNumber) {
        Notiflix.Notify.warning('Please enter tracking number');
        return;
    }

    Notiflix.Loading.circle('Updating shipping info...');

    $.ajax({
        url: `/admin/orders/${currentOrderId}/status`,
        type: 'POST',
        data: {
            status: 'shipped',
            tracking_number: trackingNumber,
            notes: notes || 'Order shipped with tracking number'
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            Notiflix.Loading.remove();

            if (response.success) {
                Notiflix.Notify.success('Order marked as shipped with tracking number!');
                shippingModal.hide();
                $("#jsGrid").jsGrid("loadData");
            } else {
                Notiflix.Notify.failure(response.message);
            }
        },
        error: function(xhr) {
            Notiflix.Loading.remove();
            const response = xhr.responseJSON;
            Notiflix.Notify.failure(response?.message || 'Failed to update shipping info');
        }
    });
}

function refreshOrders() {
    $("#jsGrid").jsGrid("loadData");
    Notiflix.Notify.success('Orders refreshed');
}

// Helper functions
function getStatusBadge(status) {
    const badges = {
        pending: '<span class="badge bg-warning">Pending</span>',
        paid: '<span class="badge bg-info">Paid</span>',
        processing: '<span class="badge bg-primary">Processing</span>',
        shipped: '<span class="badge bg-secondary">Shipped</span>',
        delivered: '<span class="badge bg-success">Delivered</span>',
        cancelled: '<span class="badge bg-danger">Cancelled</span>',
        refunded: '<span class="badge bg-dark">Refunded</span>'
    };
    return badges[status] || '<span class="badge bg-light">Unknown</span>';
}

function formatDateTime(dateTime) {
    if (!dateTime) return 'N/A';
    const date = new Date(dateTime);
    return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
}
</script>
@endpush