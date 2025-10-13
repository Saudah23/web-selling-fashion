@extends('layouts.app')

@section('title', 'Banner Management - Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">
                        <i class="fas fa-images me-2"></i>Homepage Banners
                    </div>
                    <div class="card-tools">
                        <button class="btn btn-primary btn-round btn-sm" onclick="openCreateModal()">
                            <i class="fas fa-plus me-2"></i>Add Banner
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
<div class="modal fade" id="bannerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Banner</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="bannerForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="bannerId">

                    <!-- Basic Information -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" required>
                            <div class="invalid-feedback" id="titleError"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="subtitle" class="form-label">Subtitle</label>
                            <input type="text" class="form-control" id="subtitle" name="subtitle">
                            <div class="invalid-feedback" id="subtitleError"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            <div class="invalid-feedback" id="descriptionError"></div>
                        </div>
                    </div>

                    <!-- Image Upload -->
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="image" class="form-label">Banner Image <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                            <div class="invalid-feedback" id="imageError"></div>
                            <small class="text-muted">Recommended size: 1920x600px. Max: 5MB. Formats: JPEG, PNG, GIF, WebP</small>

                            <!-- Image Preview -->
                            <div id="imagePreview" class="mt-3" style="display: none;">
                                <img id="previewImg" src="" alt="Preview" class="img-thumbnail" style="max-height: 200px;">
                            </div>
                        </div>
                    </div>

                    <!-- Button Settings -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="button_text" class="form-label">Button Text</label>
                            <input type="text" class="form-control" id="button_text" name="button_text">
                            <div class="invalid-feedback" id="buttonTextError"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="button_style" class="form-label">Button Style</label>
                            <select class="form-select" id="button_style" name="button_style">
                                <option value="primary">Primary (Blue)</option>
                                <option value="secondary">Secondary (Orange)</option>
                                <option value="outline">Outline (White)</option>
                            </select>
                            <div class="invalid-feedback" id="buttonStyleError"></div>
                        </div>
                    </div>

                    <!-- Style Settings -->
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="text_position" class="form-label">Text Position <span class="text-danger">*</span></label>
                            <select class="form-select" id="text_position" name="text_position" required>
                                <option value="left">Left</option>
                                <option value="center">Center</option>
                                <option value="right">Right</option>
                            </select>
                            <div class="invalid-feedback" id="textPositionError"></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="text_color" class="form-label">Text Color</label>
                            <input type="color" class="form-control form-control-color" id="text_color" name="text_color" value="#ffffff">
                            <div class="invalid-feedback" id="textColorError"></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="background_color" class="form-label">Background Color</label>
                            <input type="color" class="form-control form-control-color" id="background_color" name="background_color">
                            <div class="invalid-feedback" id="backgroundColorError"></div>
                        </div>
                    </div>

                    <!-- Status & Order -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="sort_order" class="form-label">Sort Order</label>
                            <input type="number" class="form-control" id="sort_order" name="sort_order" value="0" min="0">
                            <div class="invalid-feedback" id="sortOrderError"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                <label class="form-check-label" for="is_active">
                                    Active
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Save Banner</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Details Modal -->
<div class="modal fade" id="viewBannerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Banner Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewBannerContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Banner management specific styles */
#bannerModal .modal-body {
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

/* JSGrid responsive improvements for banner table */
@media (max-width: 992px) {
    .title-column {
        min-width: 120px;
    }

    .subtitle-column,
    .button-column {
        display: none;
    }

    .position-column,
    .sort-column {
        min-width: 70px;
    }
}

@media (max-width: 768px) {
    .subtitle-column,
    .button-column,
    .position-column {
        display: none;
    }

    .title-column {
        min-width: 100px;
    }
}

/* Banner preview styles */
.banner-preview {
    max-width: 100px;
    max-height: 60px;
    object-fit: cover;
    border-radius: 4px;
}

/* Color input improvements */
.form-control-color {
    width: 60px;
    height: 40px;
    border-radius: 6px;
}
</style>
@endpush

@push('scripts')
<script>
let isEditMode = false;

$(document).ready(function() {
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
                    url: "{{ route('admin.banners.data') }}",
                    data: filter,
                    dataType: "json"
                });
            }
        },

        fields: [
            {
                name: "image_preview",
                title: "Image",
                type: "text",
                width: 80,
                minWidth: 60,
                filtering: false,
                sorting: false,
                css: "image-column text-center",
                headerCss: "text-center",
                itemTemplate: function(value, item) {
                    return `<img src="${value}" alt="Banner" class="banner-preview">`;
                }
            },
            {
                name: "title",
                title: "Title",
                type: "text",
                width: 150,
                minWidth: 120,
                filtering: true,
                css: "title-column"
            },
            {
                name: "subtitle",
                title: "Subtitle",
                type: "text",
                width: 120,
                minWidth: 100,
                filtering: false,
                css: "subtitle-column",
                itemTemplate: function(value) {
                    return value ? `<small class="text-muted">${value}</small>` : '-';
                }
            },
            {
                name: "button_text",
                title: "Button",
                type: "text",
                width: 100,
                minWidth: 80,
                filtering: false,
                css: "button-column",
                itemTemplate: function(value) {
                    return value ? `<code class="text-primary">${value}</code>` : '-';
                }
            },
            {
                name: "text_position",
                title: "Position",
                type: "select",
                width: 80,
                minWidth: 70,
                filtering: true,
                css: "position-column text-center",
                headerCss: "text-center",
                items: [
                    { Name: "", Id: "" },
                    { Name: "Left", Id: "left" },
                    { Name: "Center", Id: "center" },
                    { Name: "Right", Id: "right" }
                ],
                valueField: "Id",
                textField: "Name",
                itemTemplate: function(value) {
                    const badges = {
                        'left': '<span class="badge bg-info">Left</span>',
                        'center': '<span class="badge bg-warning">Center</span>',
                        'right': '<span class="badge bg-success">Right</span>'
                    };
                    return badges[value] || '-';
                }
            },
            {
                name: "is_active",
                title: "Status",
                type: "select",
                width: 80,
                minWidth: 70,
                filtering: true,
                css: "status-column text-center",
                headerCss: "text-center",
                items: [
                    { Name: "", Id: "" },
                    { Name: "Active", Id: "true" },
                    { Name: "Inactive", Id: "false" }
                ],
                valueField: "Id",
                textField: "Name",
                itemTemplate: function(value) {
                    return value ?
                        '<span class="badge bg-success badge-sm">Active</span>' :
                        '<span class="badge bg-danger badge-sm">Inactive</span>';
                }
            },
            {
                name: "sort_order",
                title: "Order",
                type: "number",
                width: 70,
                minWidth: 60,
                filtering: false,
                css: "sort-column text-center",
                headerCss: "text-center"
            },
            {
                type: "control",
                title: "Actions",
                width: 120,
                minWidth: 100,
                css: "actions-column text-center",
                headerCss: "text-center",
                itemTemplate: function(value, item) {
                    return '<div class="btn-group btn-group-sm" role="group">' +
                           '<button type="button" class="btn btn-outline-info btn-sm" onclick="viewBanner(' + item.id + ')" title="View">' +
                           '<i class="fas fa-eye"></i>' +
                           '</button>' +
                           '<button type="button" class="btn btn-outline-primary btn-sm" onclick="editBanner(' + item.id + ')" title="Edit">' +
                           '<i class="fas fa-edit"></i>' +
                           '</button>' +
                           '<button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteBanner(' + item.id + ')" title="Delete">' +
                           '<i class="fas fa-trash"></i>' +
                           '</button>' +
                           '</div>';
                }
            }
        ]
    });

    // Form submission
    $('#bannerForm').on('submit', function(e) {
        e.preventDefault();

        // Clear previous validation errors
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        const formData = new FormData(this);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        // Handle checkbox - FormData doesn't include unchecked checkboxes
        formData.append('is_active', $('#is_active').prop('checked') ? '1' : '0');

        if (isEditMode) {
            formData.append('_method', 'PUT');
        }

        const url = isEditMode
            ? `/admin/banners/${$('#bannerId').val()}`
            : "{{ route('admin.banners.store') }}";

        Notiflix.Loading.circle('Saving banner...');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Notiflix.Loading.remove();
                if (response.success) {
                    Notiflix.Notify.success(response.message);
                    $('#bannerModal').modal('hide');
                    $("#jsGrid").jsGrid("loadData");
                    resetForm();
                }
            },
            error: function(xhr) {
                Notiflix.Loading.remove();
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    for (const field in errors) {
                        $(`#${field}`).addClass('is-invalid');
                        $(`#${field}Error`).text(errors[field][0]);
                    }
                } else {
                    Notiflix.Notify.failure(xhr.responseJSON?.message || 'An error occurred');
                }
            }
        });
    });

    // Image preview
    $('#image').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#previewImg').attr('src', e.target.result);
                $('#imagePreview').show();
            };
            reader.readAsDataURL(file);
        } else {
            $('#imagePreview').hide();
        }
    });
});

// Open create modal
function openCreateModal() {
    isEditMode = false;
    resetForm();
    $('#modalTitle').text('Add Banner');
    $('#submitBtn').text('Save Banner');
    // Image is required for create
    $('#image').prop('required', true);
    $('#bannerModal').modal('show');
}

// View banner details
function viewBanner(id) {
    $.ajax({
        url: `/admin/banners/${id}`,
        type: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                const banner = response.data;
                $('#viewBannerContent').html(`
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <img src="${banner.image_url}" alt="Banner" class="img-fluid rounded" style="max-height: 200px;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr><td><strong>Title:</strong></td><td>${banner.title}</td></tr>
                                <tr><td><strong>Subtitle:</strong></td><td>${banner.subtitle || '-'}</td></tr>
                                <tr><td><strong>Button:</strong></td><td>${banner.button_text || '-'}</td></tr>
                                <tr><td><strong>Position:</strong></td><td>${banner.text_position}</td></tr>
                                <tr><td><strong>Status:</strong></td><td>${banner.is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>'}</td></tr>
                                <tr><td><strong>Order:</strong></td><td>${banner.sort_order}</td></tr>
                            </table>
                        </div>
                    </div>
                    ${banner.description ? `<div class="row mt-3"><div class="col-12"><strong>Description:</strong><br><p class="mt-2">${banner.description}</p></div></div>` : ''}
                `);
                $('#viewBannerModal').modal('show');
            }
        },
        error: function(xhr) {
            Notiflix.Notify.failure('Failed to load banner details');
        }
    });
}

// Edit banner
function editBanner(id) {
    $.ajax({
        url: `/admin/banners/${id}`,
        type: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                const banner = response.data;
                isEditMode = true;

                $('#bannerId').val(banner.id);
                $('#title').val(banner.title);
                $('#subtitle').val(banner.subtitle);
                $('#description').val(banner.description);
                $('#button_text').val(banner.button_text);
                $('#button_style').val(banner.button_style);
                $('#text_position').val(banner.text_position);
                $('#text_color').val(banner.text_color);
                $('#background_color').val(banner.background_color);
                $('#is_active').prop('checked', banner.is_active);
                $('#sort_order').val(banner.sort_order);

                // Clear image field and show current image
                $('#image').val('');
                // Image is optional for edit
                $('#image').prop('required', false);
                if (banner.image_url) {
                    $('#previewImg').attr('src', banner.image_url);
                    $('#imagePreview').show();
                }

                $('#modalTitle').text('Edit Banner');
                $('#submitBtn').text('Update Banner');
                $('#bannerModal').modal('show');
            }
        },
        error: function(xhr) {
            Notiflix.Notify.failure('Failed to load banner data');
        }
    });
}

// Delete banner
function deleteBanner(id) {
    Notiflix.Confirm.show(
        'Confirm Delete',
        'Are you sure you want to delete this banner? This action cannot be undone.',
        'Yes, Delete',
        'Cancel',
        function() {
            $.ajax({
                url: `/admin/banners/${id}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        Notiflix.Notify.success(response.message);
                        $("#jsGrid").jsGrid("loadData");
                    }
                },
                error: function(xhr) {
                    Notiflix.Notify.failure(xhr.responseJSON?.message || 'Failed to delete banner');
                }
            });
        }
    );
}

// Reset form
function resetForm() {
    $('#bannerForm')[0].reset();
    $('#bannerId').val('');
    $('.form-control').removeClass('is-invalid');
    $('.invalid-feedback').text('');
    $('#imagePreview').hide();
    $('#text_color').val('#ffffff');
    $('#is_active').prop('checked', true);
    // Reset image required attribute (will be set by openCreateModal or editBanner)
    $('#image').prop('required', true);
}
</script>
@endpush