@extends('layouts.app')

@section('title', 'Manajemen Produk - Dashboard Admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">
                        <i class="fas fa-box me-2"></i>Daftar Produk
                    </div>
                    <div class="card-tools">
                        <button class="btn btn-success btn-round btn-sm me-2" onclick="showBulkStockModal()">
                            <i class="fas fa-boxes me-2"></i>Update Stok Massal
                        </button>
                        <button class="btn btn-primary btn-round btn-sm" onclick="openCreateModal()">
                            <i class="fas fa-plus me-2"></i>Tambah Produk
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

<!-- Create/Edit Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="productForm">
                <div class="modal-body">
                    <input type="hidden" id="productId">

                    <!-- Basic Information Tab -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div class="invalid-feedback" id="nameError"></div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="sku" class="form-label">SKU <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="sku" name="sku" required>
                            <div class="invalid-feedback" id="skuError"></div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="">-- Pilih Kategori --</option>
                            </select>
                            <div class="invalid-feedback" id="categoryIdError"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="short_description" class="form-label">Deskripsi Singkat</label>
                            <textarea class="form-control" id="short_description" name="short_description" rows="2" maxlength="500"></textarea>
                            <div class="invalid-feedback" id="shortDescriptionError"></div>
                            <small class="text-muted">Maksimal 500 karakter</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                            <div class="invalid-feedback" id="descriptionError"></div>
                        </div>
                    </div>

                    <!-- Pricing & Stock -->
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="price" class="form-label">Harga <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="price" name="price" min="0" step="0.01" required>
                            </div>
                            <div class="invalid-feedback" id="priceError"></div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="compare_price" class="form-label">Harga Banding</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="compare_price" name="compare_price" min="0" step="0.01">
                            </div>
                            <div class="invalid-feedback" id="comparePriceError"></div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="stock_quantity" class="form-label">Jumlah Stok <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" min="0" required>
                            <div class="invalid-feedback" id="stockQuantityError"></div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="min_stock_level" class="form-label">Level Stok Min <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="min_stock_level" name="min_stock_level" min="0" required>
                            <div class="invalid-feedback" id="minStockLevelError"></div>
                        </div>
                    </div>

                    <!-- Physical Properties -->
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="weight" class="form-label">Berat (kg)</label>
                            <input type="number" class="form-control" id="weight" name="weight" min="0" step="0.01">
                            <div class="invalid-feedback" id="weightError"></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="dimensions" class="form-label">Dimensi</label>
                            <input type="text" class="form-control" id="dimensions" name="dimensions" placeholder="contoh: S, M, L, XL">
                            <div class="invalid-feedback" id="dimensionsError"></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="sort_order" class="form-label">Urutan</label>
                            <input type="number" class="form-control" id="sort_order" name="sort_order" value="0" min="0">
                            <div class="invalid-feedback" id="sortOrderError"></div>
                        </div>
                    </div>

                    <!-- Status & Features -->
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                <label class="form-check-label" for="is_active">
                                    Aktif
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured">
                                <label class="form-check-label" for="is_featured">
                                    Unggulan
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Image Info for New Products -->
                    <div class="row" id="imageInfoSection">
                        <div class="col-12">
                            <hr class="my-4">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Gambar Produk:</strong> Simpan produk terlebih dahulu untuk mengunggah gambar. Anda dapat menambahkan beberapa gambar dan menetapkan satu sebagai gambar utama.
                            </div>
                        </div>
                    </div>

                    <!-- Product Images Section -->
                    <div class="row" id="productImagesSection" style="display: none;">
                        <div class="col-12">
                            <hr class="my-4">
                            <h6 class="mb-3">
                                <i class="fas fa-images me-2"></i>Gambar Produk
                            </h6>

                            <!-- Image Upload Area -->
                            <div class="mb-4">
                                <div class="image-upload-container">
                                    <div class="image-dropzone" id="imageDropzone">
                                        <div class="text-center py-4">
                                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                            <h5>Seret & Lepas Gambar di Sini</h5>
                                            <p class="text-muted">atau <button type="button" class="btn btn-link p-0" onclick="document.getElementById('imageInput').click()">pilih file</button></p>
                                            <small class="text-muted">
                                                Dukungan: JPEG, PNG, WebP, GIF | Maks: 5MB per file | Maks: 5 file
                                            </small>
                                        </div>
                                        <input type="file" id="imageInput" name="images[]" multiple accept="image/*" style="display: none;">
                                    </div>
                                </div>
                            </div>

                            <!-- Selected Images Preview -->
                            <div id="selectedImagesPreview" style="display: none;">
                                <h6 class="mb-3">Gambar Terpilih</h6>
                                <div id="imagePreviewContainer" class="row g-2">
                                    <!-- Dynamic content -->
                                </div>
                                <div class="mt-3">
                                    <button type="button" class="btn btn-primary btn-sm" onclick="uploadImages()">
                                        <i class="fas fa-upload me-2"></i>Unggah Gambar
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="clearSelectedImages()">
                                        <i class="fas fa-times me-2"></i>Hapus Semua
                                    </button>
                                </div>
                            </div>

                            <!-- Existing Images -->
                            <div id="existingImagesContainer">
                                <h6 class="mb-3">Gambar Saat Ini</h6>
                                <div id="existingImagesGrid" class="row g-2">
                                    <!-- Dynamic content -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Simpan Produk</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Product Detail Modal -->
<div class="modal fade" id="productDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="productDetailContent">
                <!-- Dynamic content -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Stock Update Modal -->
<div class="modal fade" id="bulkStockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Stok Massal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Pilih produk dan masukkan jumlah stok baru. Hanya produk aktif yang ditampilkan.
                </div>
                <div id="bulkStockContent">
                    <div class="text-center py-4">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat produk...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="updateBulkStockBtn">Perbarui Stok</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Product specific styles */
#productModal .modal-body {
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

.stock-low {
    color: #dc3545;
    font-weight: bold;
}

.stock-normal {
    color: #198754;
}

.bulk-stock-item {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 1rem;
    margin-bottom: 1rem;
}

.bulk-stock-item:last-child {
    margin-bottom: 0;
}

.bulk-stock-item.selected {
    border-color: #0d6efd;
    background-color: #f8f9fa;
}

/* Image Upload Styles */
.image-upload-container {
    border: 2px dashed #dee2e6;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
}

.image-dropzone {
    padding: 2rem;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.image-dropzone:hover {
    background-color: #f8f9fa;
    border-color: #0d6efd;
}

.image-dropzone.dragover {
    background-color: #e3f2fd;
    border-color: #2196f3;
    border-style: solid;
}

.image-preview-card {
    position: relative;
    border: 2px solid #dee2e6;
    border-radius: 0.5rem;
    overflow: hidden;
    transition: all 0.3s ease;
}

.image-preview-card:hover {
    border-color: #0d6efd;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.image-preview-card.primary {
    border-color: #198754;
    background-color: #f8fff9;
}

.image-preview {
    width: 100%;
    height: 150px;
    object-fit: cover;
    display: block;
}

.image-preview-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.image-preview-card:hover .image-preview-overlay {
    opacity: 1;
}

.image-actions {
    display: flex;
    gap: 0.5rem;
}

.image-actions .btn {
    border-radius: 50%;
    width: 36px;
    height: 36px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.primary-badge {
    position: absolute;
    top: 8px;
    left: 8px;
    background: #198754;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 600;
}

.image-info {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(transparent, rgba(0,0,0,0.8));
    color: white;
    padding: 1rem 0.5rem 0.5rem;
    font-size: 0.75rem;
}

.upload-progress {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: #e9ecef;
}

.upload-progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #0d6efd, #198754);
    transition: width 0.3s ease;
    width: 0;
}

.sortable-ghost {
    opacity: 0.5;
}

.sortable-chosen {
    transform: rotate(5deg);
}
</style>
@endpush

@push('scripts')
<script>
let productModal;
let productDetailModal;
let bulkStockModal;
let isEditMode = false;
let categories = [];
let selectedFiles = [];
let currentProductId = null;

$(document).ready(function() {
    // Initialize modals
    productModal = new bootstrap.Modal(document.getElementById('productModal'));
    productDetailModal = new bootstrap.Modal(document.getElementById('productDetailModal'));
    bulkStockModal = new bootstrap.Modal(document.getElementById('bulkStockModal'));

    // Load categories
    loadCategories();

    // Initialize JSGrid with responsive configuration
    $("#jsGrid").jsGrid({
        height: "auto",
        width: "100%",

        filtering: true,
        editing: false,
        sorting: true,
        paging: true,
        pageSize: 10,
        pageButtonCount: 5,

        autoload: true,
        controller: {
            loadData: function(filter) {
                return $.ajax({
                    type: "GET",
                    url: "{{ route('admin.products.data') }}",
                    data: filter,
                    dataType: "json"
                });
            }
        },

        fields: [
            {
                name: "name",
                title: "Nama Produk",
                type: "text",
                width: 150,
                minWidth: 120,
                filtering: true,
                css: "name-column"
            },
            {
                name: "sku",
                title: "SKU",
                type: "text",
                width: 100,
                minWidth: 80,
                filtering: true,
                css: "sku-column",
                itemTemplate: function(value) {
                    return '<code class="text-muted small">' + value + '</code>';
                }
            },
            {
                name: "category_name",
                title: "Kategori",
                type: "text",
                width: 100,
                minWidth: 80,
                filtering: false,
                css: "category-column"
            },
            {
                name: "price",
                title: "Harga",
                type: "text",
                width: 90,
                minWidth: 80,
                filtering: false,
                css: "price-column text-end",
                headerCss: "text-end",
                itemTemplate: function(value) {
                    return '<strong>' + value + '</strong>';
                }
            },
            {
                name: "stock_quantity",
                title: "Stok",
                type: "number",
                width: 70,
                minWidth: 60,
                filtering: false,
                css: "stock-column text-center",
                headerCss: "text-center",
                itemTemplate: function(value, item) {
                    const className = item.is_low_stock ? 'stock-low' : 'stock-normal';
                    const icon = item.is_low_stock ? '<i class="fas fa-exclamation-triangle me-1"></i>' : '';
                    return '<span class="' + className + '">' + icon + value + '</span>';
                }
            },
            {
                name: "is_featured",
                title: "Unggulan",
                type: "select",
                width: 80,
                minWidth: 70,
                filtering: true,
                css: "featured-column text-center",
                headerCss: "text-center",
                items: [
                    { Name: "", Id: "" },
                    { Name: "Ya", Id: "true" },
                    { Name: "Tidak", Id: "false" }
                ],
                valueField: "Id",
                textField: "Name",
                itemTemplate: function(value) {
                    return value ?
                        '<i class="fas fa-star text-warning" title="Unggulan"></i>' :
                        '<i class="far fa-star text-muted" title="Tidak Unggulan"></i>';
                }
            },
            {
                name: "is_active",
                title: "Status",
                type: "select",
                width: 80,
                minWidth: 70,
                filtering: true,
                css: "status-column",
                items: [
                    { Name: "", Id: "" },
                    { Name: "Aktif", Id: "true" },
                    { Name: "Tidak Aktif", Id: "false" }
                ],
                valueField: "Id",
                textField: "Name",
                itemTemplate: function(value) {
                    return value ?
                        '<span class="badge bg-success badge-sm">Aktif</span>' :
                        '<span class="badge bg-danger badge-sm">Tidak Aktif</span>';
                }
            },
            {
                type: "control",
                title: "Aksi",
                width: 120,
                minWidth: 100,
                css: "actions-column text-center",
                headerCss: "text-center",
                itemTemplate: function(value, item) {
                    return '<div class="btn-group btn-group-sm" role="group">' +
                           '<button type="button" class="btn btn-outline-info btn-sm" onclick="viewProduct(' + item.id + ')" title="Lihat">' +
                           '<i class="fas fa-eye"></i>' +
                           '</button>' +
                           '<button type="button" class="btn btn-outline-primary btn-sm" onclick="editProduct(' + item.id + ')" title="Edit">' +
                           '<i class="fas fa-edit"></i>' +
                           '</button>' +
                           '<button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteProduct(' + item.id + ')" title="Hapus">' +
                           '<i class="fas fa-trash"></i>' +
                           '</button>' +
                           '</div>';
                }
            }
        ]
    });

    // Form submission
    $('#productForm').on('submit', function(e) {
        e.preventDefault();
        submitForm();
    });

    // Bulk stock update
    $('#updateBulkStockBtn').on('click', function() {
        updateBulkStock();
    });

    // Image upload functionality
    initializeImageUpload();
});

function loadCategories() {
    $.ajax({
        url: "{{ route('admin.products.categories') }}",
        type: 'GET',
        success: function(response) {
            if (response.success) {
                categories = response.data;
                const select = $('#category_id');
                select.empty();
                select.append('<option value="">-- Select Category --</option>');

                response.data.forEach(function(category) {
                    select.append(`<option value="${category.id}">${category.name}</option>`);
                });
            }
        }
    });
}

function openCreateModal() {
    isEditMode = false;
    currentProductId = null;
    $('#modalTitle').text('Add Product');
    $('#submitBtn').text('Save Product');
    $('#productForm')[0].reset();
    $('#productId').val('');
    $('#is_active').prop('checked', true);
    $('#is_featured').prop('checked', false);
    clearValidationErrors();

    // Show info section for new products, hide image section
    $('#imageInfoSection').show();
    $('#productImagesSection').hide();
    clearSelectedImages();

    productModal.show();
}

function editProduct(id) {
    isEditMode = true;
    currentProductId = id;
    $('#modalTitle').text('Edit Product');
    $('#submitBtn').text('Update Product');

    $.ajax({
        url: `/admin/products/${id}`,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                const product = response.data;
                $('#productId').val(product.id);
                $('#name').val(product.name);
                $('#sku').val(product.sku);
                $('#category_id').val(product.category_id);
                $('#description').val(product.description);
                $('#short_description').val(product.short_description);
                $('#price').val(product.price);
                $('#compare_price').val(product.compare_price);
                $('#stock_quantity').val(product.stock_quantity);
                $('#min_stock_level').val(product.min_stock_level);
                $('#weight').val(product.weight);
                $('#dimensions').val(product.dimensions);
                $('#sort_order').val(product.sort_order);
                $('#is_active').prop('checked', product.is_active);
                $('#is_featured').prop('checked', product.is_featured);

                // Show image section for existing products
                $('#productImagesSection').show();
                clearSelectedImages();
                loadExistingImages();

                clearValidationErrors();
                productModal.show();
            }
        },
        error: function() {
            Notiflix.Notify.failure('Failed to load product data');
        }
    });
}

function viewProduct(id) {
    $.ajax({
        url: `/admin/products/${id}`,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                const product = response.data;

                let content = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-info-circle me-2"></i>Basic Information</h6>
                            <table class="table table-sm">
                                <tr><td><strong>Name:</strong></td><td>${product.name}</td></tr>
                                <tr><td><strong>SKU:</strong></td><td><code>${product.sku}</code></td></tr>
                                <tr><td><strong>Category:</strong></td><td>${product.category ? product.category.name : '-'}</td></tr>
                                <tr><td><strong>Status:</strong></td><td>${product.is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>'}</td></tr>
                                <tr><td><strong>Featured:</strong></td><td>${product.is_featured ? '<i class="fas fa-star text-warning"></i> Yes' : 'No'}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-dollar-sign me-2"></i>Pricing & Stock</h6>
                            <table class="table table-sm">
                                <tr><td><strong>Price:</strong></td><td>Rp ${product.price ? Number(product.price).toLocaleString('id-ID') : '0'}</td></tr>
                                <tr><td><strong>Compare Price:</strong></td><td>${product.compare_price ? 'Rp ' + Number(product.compare_price).toLocaleString('id-ID') : '-'}</td></tr>
                                <tr><td><strong>Stock:</strong></td><td>${product.stock_quantity}</td></tr>
                                <tr><td><strong>Min Stock:</strong></td><td>${product.min_stock_level}</td></tr>
                                <tr><td><strong>Weight:</strong></td><td>${product.weight ? product.weight + ' kg' : '-'}</td></tr>
                                <tr><td><strong>Dimensions:</strong></td><td>${product.dimensions || '-'}</td></tr>
                            </table>
                        </div>
                    </div>
                `;

                if (product.short_description) {
                    content += `
                        <div class="row mt-3">
                            <div class="col-12">
                                <h6><i class="fas fa-align-left me-2"></i>Short Description</h6>
                                <p class="border-start border-primary border-4 ps-3">${product.short_description}</p>
                            </div>
                        </div>
                    `;
                }

                if (product.description) {
                    content += `
                        <div class="row mt-3">
                            <div class="col-12">
                                <h6><i class="fas fa-file-text me-2"></i>Description</h6>
                                <div class="border p-3 rounded bg-light">
                                    <p class="mb-0">${product.description}</p>
                                </div>
                            </div>
                        </div>
                    `;
                }

                // Load and show product images
                $.ajax({
                    url: `/admin/products/${id}/images`,
                    type: 'GET',
                    success: function(imageResponse) {
                        if (imageResponse.success && imageResponse.data.length > 0) {
                            content += `
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h6><i class="fas fa-images me-2"></i>Product Images</h6>
                                        <div class="row g-2">
                            `;

                            imageResponse.data.forEach(image => {
                                const isPrimary = image.is_primary;
                                const imageUrl = image.url.startsWith('http') ? image.url : `/${image.url}`;
                                content += `
                                    <div class="col-md-3 col-sm-4 col-6">
                                        <div class="position-relative">
                                            <img src="${imageUrl}" class="img-thumbnail" style="width: 100%; height: 120px; object-fit: cover;" alt="${image.alt_text || 'Product image'}"
                                                 onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTIwIiBoZWlnaHQ9IjEyMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsbD0iI2VlZSIvPgo8dGV4dCB4PSI1MCUiIHk9IjUwJSIgZm9udC1mYW1pbHk9IkFyaWFsLCBzYW5zLXNlcmlmIiBmb250LXNpemU9IjEyIiBmaWxsPSIjOTk5IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBkeT0iLjNlbSI+Tm8gSW1hZ2U8L3RleHQ+Cjwvc3ZnPg==';">
                                            ${isPrimary ? '<span class="position-absolute top-0 start-0 badge bg-success m-1">Primary</span>' : ''}
                                        </div>
                                        <small class="text-muted d-block mt-1">${image.filename || 'Unknown'}</small>
                                    </div>
                                `;
                            });

                            content += `
                                        </div>
                                    </div>
                                </div>
                            `;
                        }

                        $('#productDetailContent').html(content);
                        productDetailModal.show();
                    },
                    error: function() {
                        $('#productDetailContent').html(content);
                        productDetailModal.show();
                    }
                });
            }
        },
        error: function() {
            Notiflix.Notify.failure('Failed to load product data');
        }
    });
}

function deleteProduct(id) {
    Notiflix.Confirm.show(
        'Confirm Delete',
        'Are you sure you want to delete this product?',
        'Yes, Delete',
        'Cancel',
        function() {
            $.ajax({
                url: `/admin/products/${id}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        Notiflix.Notify.success(response.message);
                        $("#jsGrid").jsGrid("loadData");
                    } else {
                        Notiflix.Notify.failure(response.message);
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    Notiflix.Notify.failure(response?.message || 'Failed to delete product');
                }
            });
        }
    );
}

function showBulkStockModal() {
    $('#bulkStockContent').html(`
        <div class="text-center py-4">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading products...</p>
        </div>
    `);

    bulkStockModal.show();

    // Load active products
    $.ajax({
        url: "{{ route('admin.products.data') }}",
        type: 'GET',
        data: { is_active: 'true' },
        success: function(products) {
            let content = '';

            if (products.length === 0) {
                content = '<div class="alert alert-warning">No active products found.</div>';
            } else {
                products.forEach(function(product) {
                    const lowStockBadge = product.is_low_stock ? '<span class="badge bg-warning ms-2">Low Stock</span>' : '';
                    content += `
                        <div class="bulk-stock-item" data-product-id="${product.id}">
                            <div class="row align-items-center">
                                <div class="col-md-1">
                                    <div class="form-check">
                                        <input class="form-check-input bulk-select" type="checkbox" value="${product.id}">
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <strong>${product.name}</strong><br>
                                    <small class="text-muted">SKU: ${product.sku}</small>
                                </div>
                                <div class="col-md-3">
                                    <span>Current: <strong>${product.stock_quantity}</strong></span>
                                    ${lowStockBadge}
                                </div>
                                <div class="col-md-3">
                                    <input type="number" class="form-control form-control-sm stock-input"
                                           min="0" value="${product.stock_quantity}" disabled>
                                </div>
                            </div>
                        </div>
                    `;
                });
            }

            $('#bulkStockContent').html(content);

            // Enable/disable stock input based on checkbox
            $('.bulk-select').on('change', function() {
                const productItem = $(this).closest('.bulk-stock-item');
                const stockInput = productItem.find('.stock-input');

                if ($(this).is(':checked')) {
                    productItem.addClass('selected');
                    stockInput.prop('disabled', false);
                } else {
                    productItem.removeClass('selected');
                    stockInput.prop('disabled', true);
                }
            });
        },
        error: function() {
            $('#bulkStockContent').html('<div class="alert alert-danger">Failed to load products.</div>');
        }
    });
}

function updateBulkStock() {
    const selectedProducts = [];

    $('.bulk-select:checked').each(function() {
        const productItem = $(this).closest('.bulk-stock-item');
        const productId = $(this).val();
        const stockQuantity = productItem.find('.stock-input').val();

        selectedProducts.push({
            id: productId,
            stock_quantity: parseInt(stockQuantity)
        });
    });

    if (selectedProducts.length === 0) {
        Notiflix.Notify.warning('Please select at least one product to update.');
        return;
    }

    Notiflix.Loading.circle('Updating stock...');

    $.ajax({
        url: "{{ route('admin.products.bulk-update-stock') }}",
        type: 'POST',
        data: {
            products: selectedProducts
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            Notiflix.Loading.remove();

            if (response.success) {
                Notiflix.Notify.success(response.message);
                bulkStockModal.hide();
                $("#jsGrid").jsGrid("loadData");
            } else {
                Notiflix.Notify.failure(response.message);
            }
        },
        error: function(xhr) {
            Notiflix.Loading.remove();
            const response = xhr.responseJSON;
            Notiflix.Notify.failure(response?.message || 'Failed to update stock');
        }
    });
}

function submitForm() {
    // Clear previous validation errors
    clearValidationErrors();

    const formData = {
        name: $('#name').val().trim(),
        sku: $('#sku').val().trim(),
        category_id: $('#category_id').val(),
        description: $('#description').val().trim(),
        short_description: $('#short_description').val().trim(),
        price: parseFloat($('#price').val()) || 0,
        compare_price: parseFloat($('#compare_price').val()) || null,
        stock_quantity: parseInt($('#stock_quantity').val()) || 0,
        min_stock_level: parseInt($('#min_stock_level').val()) || 0,
        weight: parseFloat($('#weight').val()) || null,
        dimensions: $('#dimensions').val().trim(),
        sort_order: parseInt($('#sort_order').val()) || 0,
        is_active: $('#is_active').is(':checked') ? 1 : 0,
        is_featured: $('#is_featured').is(':checked') ? 1 : 0
    };

    // Client-side validation
    if (!formData.name) {
        showValidationErrors({name: ['Product name is required']});
        return;
    }
    if (!formData.sku) {
        showValidationErrors({sku: ['SKU is required']});
        return;
    }
    if (!formData.category_id) {
        showValidationErrors({category_id: ['Category is required']});
        return;
    }

    const url = isEditMode ? `/admin/products/${$('#productId').val()}` : '/admin/products';
    const method = isEditMode ? 'PUT' : 'POST';

    // Show loading
    Notiflix.Loading.circle('Saving product...');

    $.ajax({
        url: url,
        type: method,
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        success: function(response) {
            Notiflix.Loading.remove();

            if (response.success) {
                Notiflix.Notify.success(response.message);
                $("#jsGrid").jsGrid("loadData");

                // If this was a create operation, set the currentProductId for image upload
                if (!isEditMode && response.data && response.data.id) {
                    currentProductId = response.data.id;
                    isEditMode = true;
                    $('#productId').val(response.data.id);
                    $('#modalTitle').text('Edit Product - Upload Images');
                    $('#submitBtn').text('Update Product');
                    $('#productImagesSection').show();
                    loadExistingImages();

                    Notiflix.Notify.info('Product saved! You can now upload images.');
                } else {
                    // For edit operations, close modal and reset
                    productModal.hide();
                    $('#productForm')[0].reset();
                    $('#is_active').prop('checked', true);
                    $('#is_featured').prop('checked', false);
                    currentProductId = null;
                }
            } else {
                Notiflix.Notify.failure(response.message || 'Operation failed');
            }
        },
        error: function(xhr, status, error) {
            Notiflix.Loading.remove();
            console.error('AJAX Error:', {xhr, status, error});

            if (xhr.status === 422) {
                // Validation errors
                const response = xhr.responseJSON;
                if (response && response.errors) {
                    showValidationErrors(response.errors);
                } else {
                    Notiflix.Notify.failure('Validation failed. Please check your input.');
                }
            } else if (xhr.status === 419) {
                // CSRF token mismatch
                Notiflix.Notify.failure('Session expired. Please refresh the page.');
            } else {
                // Other errors
                const response = xhr.responseJSON;
                const message = response?.message || `Error ${xhr.status}: ${error}`;
                Notiflix.Notify.failure(message);
            }
        }
    });
}

function showValidationErrors(errors) {
    clearValidationErrors();

    for (const field in errors) {
        const input = $(`#${field}`);
        const errorDiv = $(`#${field}Error`);

        input.addClass('is-invalid');
        errorDiv.text(errors[field][0]);
    }
}

function clearValidationErrors() {
    $('.form-control, .form-select').removeClass('is-invalid');
    $('.invalid-feedback').text('');
}

// Notiflix is configured globally in layout template

// =============================================================================
// IMAGE UPLOAD FUNCTIONALITY
// =============================================================================

function initializeImageUpload() {
    // File input change event
    $(document).on('change', '#imageInput', function(e) {
        handleFileSelection(e.target.files);
    });

    // Drag & drop events
    $(document).on({
        dragover: function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).addClass('dragover');
        },
        dragleave: function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).removeClass('dragover');
        },
        drop: function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).removeClass('dragover');
            const files = e.originalEvent.dataTransfer.files;
            handleFileSelection(files);
        }
    }, '#imageDropzone');

    // Click to browse
    $(document).on('click', '#imageDropzone', function(e) {
        if (e.target === this || $(e.target).closest('.image-dropzone').length) {
            $('#imageInput').click();
        }
    });
}

function handleFileSelection(files) {
    const maxFiles = 5;
    const maxSize = 5 * 1024 * 1024; // 5MB
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    // Validate file count
    if (selectedFiles.length + files.length > maxFiles) {
        Notiflix.Notify.warning(`Maximum ${maxFiles} files allowed. Current: ${selectedFiles.length}`);
        return;
    }

    // Process each file
    Array.from(files).forEach(file => {
        // Validate file type
        if (!allowedTypes.includes(file.type)) {
            Notiflix.Notify.warning(`${file.name}: Unsupported file type. Use JPEG, PNG, GIF, or WebP.`);
            return;
        }

        // Validate file size
        if (file.size > maxSize) {
            Notiflix.Notify.warning(`${file.name}: File too large. Maximum 5MB allowed.`);
            return;
        }

        // Add to selected files
        selectedFiles.push({
            file: file,
            id: Date.now() + Math.random(),
            preview: null
        });
    });

    // Update preview
    updateImagePreview();
}

function updateImagePreview() {
    if (selectedFiles.length === 0) {
        $('#selectedImagesPreview').hide();
        return;
    }

    $('#selectedImagesPreview').show();
    const container = $('#imagePreviewContainer');
    container.empty();

    selectedFiles.forEach((fileData, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            fileData.preview = e.target.result;

            const card = `
                <div class="col-md-3 col-sm-4 col-6">
                    <div class="image-preview-card">
                        <img src="${e.target.result}" class="image-preview" alt="Preview">
                        <div class="image-preview-overlay">
                            <div class="image-actions">
                                <button type="button" class="btn btn-danger btn-sm"
                                        onclick="removeSelectedImage(${fileData.id})" title="Remove">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="image-info">
                            <div>${fileData.file.name}</div>
                            <div>${formatFileSize(fileData.file.size)}</div>
                        </div>
                    </div>
                </div>
            `;
            container.append(card);
        };
        reader.readAsDataURL(fileData.file);
    });
}

function removeSelectedImage(fileId) {
    selectedFiles = selectedFiles.filter(f => f.id !== fileId);
    updateImagePreview();
}

function clearSelectedImages() {
    selectedFiles = [];
    $('#selectedImagesPreview').hide();
    $('#imageInput').val('');
}

function uploadImages() {
    if (selectedFiles.length === 0) {
        Notiflix.Notify.warning('Please select images to upload.');
        return;
    }

    if (!currentProductId) {
        Notiflix.Notify.warning('Please save the product first before uploading images.');
        return;
    }

    const formData = new FormData();
    selectedFiles.forEach((fileData, index) => {
        console.log('Appending file:', fileData.file.name, fileData.file.size);
        formData.append('images[]', fileData.file);
        formData.append('alt_texts[]', fileData.file.name.split('.')[0]);
    });

    // Debug FormData
    for (let pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }

    const uploadUrl = `/admin/products/${currentProductId}/images`;
    console.log('Upload URL:', uploadUrl);
    console.log('Current Product ID:', currentProductId);

    Notiflix.Loading.circle('Uploading and optimizing images...');

    $.ajax({
        url: uploadUrl,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            console.log('Upload success:', response);
            Notiflix.Loading.remove();

            if (response.success) {
                Notiflix.Notify.success(response.message);
                clearSelectedImages();
                loadExistingImages();
            } else {
                Notiflix.Notify.failure(response.message || 'Upload failed');
            }
        },
        error: function(xhr, status, error) {
            console.error('Upload error:', xhr, status, error);
            console.error('Response:', xhr.responseText);
            Notiflix.Loading.remove();

            let errorMessage = 'Failed to upload images';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.status === 404) {
                errorMessage = 'Upload endpoint not found. Please check the product ID.';
            } else if (xhr.status === 500) {
                errorMessage = 'Server error occurred during upload.';
            }

            Notiflix.Notify.failure(errorMessage);
        }
    });
}

function loadExistingImages() {
    if (!currentProductId) {
        $('#existingImagesContainer').hide();
        return;
    }

    $('#existingImagesContainer').show();

    $.ajax({
        url: `/admin/products/${currentProductId}/images`,
        type: 'GET',
        success: function(response) {
            console.log('Load images response:', response);
            if (response.success) {
                renderExistingImages(response.data);
            } else {
                $('#existingImagesGrid').html('<div class="col-12"><div class="alert alert-warning">No images found</div></div>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Load images error:', xhr, status, error);
            $('#existingImagesGrid').html('<div class="col-12"><div class="alert alert-danger">Failed to load images</div></div>');
        }
    });
}

function renderExistingImages(images) {
    console.log('Rendering images:', images);
    const container = $('#existingImagesGrid');
    container.empty();

    if (!images || images.length === 0) {
        container.html('<div class="col-12"><div class="alert alert-info">No images uploaded yet.</div></div>');
        return;
    }

    images.forEach(image => {
        console.log('Processing image:', image);
        const isPrimary = image.is_primary;
        const imageUrl = image.url.startsWith('http') ? image.url : `/${image.url}`;
        console.log('Image URL:', imageUrl);

        const card = `
            <div class="col-md-3 col-sm-4 col-6" data-image-id="${image.id}">
                <div class="image-preview-card ${isPrimary ? 'primary' : ''}">
                    <img src="${imageUrl}" class="image-preview" alt="${image.alt_text || 'Product image'}"
                         onerror="console.error('Failed to load image:', this.src); this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTUwIiBoZWlnaHQ9IjE1MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsbD0iI2VlZSIvPgo8dGV4dCB4PSI1MCUiIHk9IjUwJSIgZm9udC1mYW1pbHk9IkFyaWFsLCBzYW5zLXNlcmlmIiBmb250LXNpemU9IjE0IiBmaWxsPSIjOTk5IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBkeT0iLjNlbSI+Tm8gSW1hZ2U8L3RleHQ+Cjwvc3ZnPg==';">
                    ${isPrimary ? '<div class="primary-badge">Primary</div>' : ''}
                    <div class="image-preview-overlay">
                        <div class="image-actions">
                            ${!isPrimary ? `<button type="button" class="btn btn-success btn-sm"
                                            onclick="setPrimaryImage(${image.id})" title="Set as Primary">
                                <i class="fas fa-star"></i>
                            </button>` : ''}
                            <button type="button" class="btn btn-danger btn-sm"
                                    onclick="deleteImage(${image.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="image-info">
                        <div>${image.filename || 'Unknown'}</div>
                        <div>${image.formatted_file_size || formatFileSize(image.file_size || 0)}</div>
                    </div>
                </div>
            </div>
        `;
        container.append(card);
    });
}

function setPrimaryImage(imageId) {
    $.ajax({
        url: `/admin/products/${currentProductId}/images/${imageId}/set-primary`,
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                Notiflix.Notify.success(response.message);
                loadExistingImages();
            } else {
                Notiflix.Notify.failure(response.message);
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            Notiflix.Notify.failure(response?.message || 'Failed to set primary image');
        }
    });
}

function deleteImage(imageId) {
    Notiflix.Confirm.show(
        'Delete Image',
        'Are you sure you want to delete this image?',
        'Yes, Delete',
        'Cancel',
        function() {
            $.ajax({
                url: `/admin/products/${currentProductId}/images/${imageId}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        Notiflix.Notify.success(response.message);
                        loadExistingImages();
                    } else {
                        Notiflix.Notify.failure(response.message);
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    Notiflix.Notify.failure(response?.message || 'Failed to delete image');
                }
            });
        }
    );
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}
</script>
@endpush