@extends('layouts.app')

@section('title', 'Category Management - Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">
                        <i class="fas fa-tags me-2"></i>Categories List
                    </div>
                    <div class="card-tools">
                        <button class="btn btn-primary btn-round btn-sm" onclick="openCreateModal()">
                            <i class="fas fa-plus me-2"></i>Add Category
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
<div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="categoryForm">
                <div class="modal-body">
                    <input type="hidden" id="categoryId">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div class="invalid-feedback" id="nameError"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="parent_id" class="form-label">Parent Category</label>
                            <select class="form-select" id="parent_id" name="parent_id">
                                <option value="">-- No Parent (Main Category) --</option>
                            </select>
                            <div class="invalid-feedback" id="parentError"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            <div class="invalid-feedback" id="descriptionError"></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="sort_order" class="form-label">Sort Order</label>
                            <input type="number" class="form-control" id="sort_order" name="sort_order" value="0" min="0">
                            <div class="invalid-feedback" id="sortOrderError"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="form-check form-switch">
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
                    <button type="submit" class="btn btn-primary" id="submitBtn">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Category specific styles */
#categoryModal .modal-body {
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
</style>
@endpush

@push('scripts')

<script>
let categoryModal;
let isEditMode = false;

$(document).ready(function() {
    // Initialize modal
    categoryModal = new bootstrap.Modal(document.getElementById('categoryModal'));

    // Load parent categories
    loadParentCategories();

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
                    url: "{{ route('admin.categories.data') }}",
                    data: filter,
                    dataType: "json"
                });
            }
        },

        fields: [
            {
                name: "name",
                title: "Name",
                type: "text",
                width: 120,
                minWidth: 100,
                filtering: true,
                css: "name-column"
            },
            {
                name: "slug",
                title: "Slug",
                type: "text",
                width: 120,
                minWidth: 100,
                filtering: false,
                css: "slug-column",
                itemTemplate: function(value) {
                    return '<code class="text-muted small">' + value + '</code>';
                }
            },
            {
                name: "parent_name",
                title: "Parent",
                type: "text",
                width: 100,
                minWidth: 80,
                filtering: false,
                css: "parent-column",
                itemTemplate: function(value) {
                    return value || '<span class="text-muted">-</span>';
                }
            },
            {
                name: "description",
                title: "Description",
                type: "text",
                width: 180,
                minWidth: 120,
                filtering: false,
                css: "description-column",
                itemTemplate: function(value) {
                    if (!value) return '<span class="text-muted">-</span>';
                    return value.length > 40 ?
                        '<span title="' + value + '">' + value.substring(0, 40) + '...</span>' :
                        value;
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
                width: 60,
                minWidth: 50,
                filtering: false,
                css: "order-column text-center",
                headerCss: "text-center"
            },
            {
                type: "control",
                title: "Actions",
                width: 100,
                minWidth: 90,
                css: "actions-column text-center",
                headerCss: "text-center",
                itemTemplate: function(value, item) {
                    return '<div class="btn-group btn-group-sm" role="group">' +
                           '<button type="button" class="btn btn-outline-primary btn-sm" onclick="editCategory(' + item.id + ')" title="Edit">' +
                           '<i class="fas fa-edit"></i>' +
                           '</button>' +
                           '<button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteCategory(' + item.id + ')" title="Delete">' +
                           '<i class="fas fa-trash"></i>' +
                           '</button>' +
                           '</div>';
                }
            }
        ]
    });

    // Form submission
    $('#categoryForm').on('submit', function(e) {
        e.preventDefault();
        submitForm();
    });
});

function loadParentCategories() {
    $.ajax({
        url: "{{ route('admin.categories.parents') }}",
        type: 'GET',
        success: function(response) {
            if (response.success) {
                const select = $('#parent_id');
                select.empty();
                select.append('<option value="">-- No Parent (Main Category) --</option>');

                response.data.forEach(function(category) {
                    select.append(`<option value="${category.id}">${category.name}</option>`);
                });
            }
        }
    });
}

function openCreateModal() {
    isEditMode = false;
    $('#modalTitle').text('Add Category');
    $('#submitBtn').text('Save Category');
    $('#categoryForm')[0].reset();
    $('#categoryId').val('');
    $('#is_active').prop('checked', true);
    clearValidationErrors();
    categoryModal.show();
}

function editCategory(id) {
    isEditMode = true;
    $('#modalTitle').text('Edit Category');
    $('#submitBtn').text('Update Category');

    $.ajax({
        url: `/admin/categories/${id}`,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                const category = response.data;
                $('#categoryId').val(category.id);
                $('#name').val(category.name);
                $('#description').val(category.description);
                $('#parent_id').val(category.parent_id);
                $('#sort_order').val(category.sort_order);
                $('#is_active').prop('checked', category.is_active);

                clearValidationErrors();
                categoryModal.show();
            }
        },
        error: function() {
            Notiflix.Notify.failure('Failed to load category data');
        }
    });
}

function deleteCategory(id) {
    Notiflix.Confirm.show(
        'Confirm Delete',
        'Are you sure you want to delete this category?',
        'Yes, Delete',
        'Cancel',
        function() {
            $.ajax({
                url: `/admin/categories/${id}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        Notiflix.Notify.success(response.message);
                        $("#jsGrid").jsGrid("loadData");
                        loadParentCategories(); // Refresh parent options
                    } else {
                        Notiflix.Notify.failure(response.message);
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    Notiflix.Notify.failure(response?.message || 'Failed to delete category');
                }
            });
        }
    );
}

function submitForm() {
    // Clear previous validation errors
    clearValidationErrors();

    const formData = {
        name: $('#name').val().trim(),
        description: $('#description').val().trim(),
        parent_id: $('#parent_id').val() || null,
        sort_order: parseInt($('#sort_order').val()) || 0,
        is_active: $('#is_active').is(':checked') ? 1 : 0
    };

    // Client-side validation
    if (!formData.name) {
        showValidationErrors({name: ['Name field is required']});
        return;
    }

    const url = isEditMode ? `/admin/categories/${$('#categoryId').val()}` : '/admin/categories';
    const method = isEditMode ? 'PUT' : 'POST';

    // Show loading
    Notiflix.Loading.circle('Saving category...');

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
                categoryModal.hide();
                $("#jsGrid").jsGrid("loadData");
                loadParentCategories(); // Refresh parent options

                // Reset form
                $('#categoryForm')[0].reset();
                $('#is_active').prop('checked', true);
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

// Notiflix is now configured globally in layout template
// No need for local configuration
</script>
@endpush