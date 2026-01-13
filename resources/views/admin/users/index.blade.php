@extends('layouts.app')

@section('title', 'Manajemen Pengguna - Dashboard Admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">
                        <i class="fas fa-users me-2"></i>Daftar Pengguna
                    </div>
                    <div class="card-tools">
                        <button class="btn btn-primary btn-round btn-sm" onclick="openCreateModal()">
                            <i class="fas fa-plus me-2"></i>Tambah Pengguna
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
<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="userForm">
                <div class="modal-body">
                    <input type="hidden" id="userId">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div class="invalid-feedback" id="nameError"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Alamat Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div class="invalid-feedback" id="emailError"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label">Peran <span class="text-danger">*</span></label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="">-- Pilih Peran --</option>
                                <option value="customer">Pelanggan</option>
                                <option value="admin">Admin</option>
                                <option value="owner">Pemilik</option>
                            </select>
                            <div class="invalid-feedback" id="roleError"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" id="email_verified" name="email_verified">
                                <label class="form-check-label" for="email_verified">
                                    Email Terverifikasi
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="passwordSection">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Kata Sandi <span class="text-danger" id="passwordRequired">*</span></label>
                            <input type="password" class="form-control" id="password" name="password">
                            <div class="invalid-feedback" id="passwordError"></div>
                            <small class="form-text text-muted" id="passwordHelp">Minimal 8 karakter</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi <span class="text-danger" id="confirmRequired">*</span></label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                            <div class="invalid-feedback" id="passwordConfirmationError"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Simpan Pengguna</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Details Modal -->
<div class="modal fade" id="viewUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewUserContent">
                <!-- Content will be loaded here -->
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
/* User management specific styles */
#userModal .modal-body {
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
    table-layout: fixed;
    width: 100% !important;
}

#jsGrid .jsgrid-header-row > .jsgrid-header-cell,
#jsGrid .jsgrid-filter-row > .jsgrid-cell,
#jsGrid .jsgrid-row > .jsgrid-cell {
    padding: 8px 6px;
    vertical-align: middle;
    text-align: center;
    border-right: 1px solid #dee2e6;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

#jsGrid .jsgrid-header-row > .jsgrid-header-cell {
    background-color: #f8f9fa;
    font-weight: 600;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Force specific column widths */
#jsGrid .name-column {
    width: 180px !important;
    min-width: 180px !important;
    max-width: 180px !important;
}

#jsGrid .email-column {
    width: 200px !important;
    min-width: 200px !important;
    max-width: 200px !important;
}

#jsGrid .role-column {
    width: 100px !important;
    min-width: 100px !important;
    max-width: 100px !important;
}

#jsGrid .email-status-column {
    width: 100px !important;
    min-width: 100px !important;
    max-width: 100px !important;
}

#jsGrid .created-column {
    width: 140px !important;
    min-width: 140px !important;
    max-width: 140px !important;
}

#jsGrid .actions-column {
    width: 120px !important;
    min-width: 120px !important;
    max-width: 120px !important;
}

/* Column specific alignment */
#jsGrid .name-column,
#jsGrid .email-column {
    text-align: left !important;
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

/* User role badges styling */
.role-owner {
    background-color: #dc2626 !important;
    color: white !important;
}

.role-admin {
    background-color: #f59e0b !important;
    color: white !important;
}

.role-customer {
    background-color: #2563eb !important;
    color: white !important;
}

/* Badge improvements */
.badge-sm {
    font-size: 0.7rem;
    padding: 0.2rem 0.4rem;
}
</style>
@endpush

@push('scripts')
<script src="{{ asset('notiflix-Notiflix-67ba12d/dist/notiflix-aio-3.2.8.min.js') }}"></script>
<script>
let isEditMode = false;

$(document).ready(function() {
    // Configure Notiflix
    Notiflix.Notify.init({
        width: '300px',
        position: 'right-top',
        distance: '20px',
        opacity: 1,
        timeout: 3000,
    });
    // Initialize JSGrid with responsive configuration following products pattern
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
                    url: "{{ route('admin.users.data') }}",
                    data: filter,
                    dataType: "json"
                });
            }
        },

        fields: [
            {
                name: "name",
                title: "Nama Lengkap",
                type: "text",
                width: 180,
                filtering: true,
                css: "name-column text-center",
                headerCss: "text-center",
                itemTemplate: function(value) {
                    return '<span class="small">' + value + '</span>';
                }
            },
            {
                name: "email",
                title: "Email",
                type: "text",
                width: 200,
                filtering: true,
                css: "email-column text-center",
                headerCss: "text-center",
                itemTemplate: function(value) {
                    return '<span class="text-muted small">' + value + '</span>';
                }
            },
            {
                name: "role",
                title: "Peran",
                type: "select",
                width: 100,
                filtering: true,
                css: "role-column text-center",
                headerCss: "text-center",
                items: [
                    { Name: "", Id: "" },
                    { Name: "Pemilik", Id: "owner" },
                    { Name: "Admin", Id: "admin" },
                    { Name: "Pelanggan", Id: "customer" }
                ],
                valueField: "Id",
                textField: "Name",
                itemTemplate: function(value, item) {
                    return item.role_badge;
                }
            },
            {
                name: "email_verified",
                title: "Status",
                type: "select",
                width: 100,
                filtering: true,
                css: "email-status-column text-center",
                headerCss: "text-center",
                items: [
                    { Name: "", Id: "" },
                    { Name: "Terverifikasi", Id: "Verified" },
                    { Name: "Belum Terverifikasi", Id: "Unverified" }
                ],
                valueField: "Id",
                textField: "Name",
                itemTemplate: function(value, item) {
                    if (value === 'Verified') {
                        return '<span class="badge bg-success badge-sm">Terverifikasi</span>';
                    } else {
                        return '<span class="badge bg-warning badge-sm">Belum Terverifikasi</span>';
                    }
                }
            },
            {
                name: "created_at",
                title: "Tanggal",
                type: "text",
                width: 140,
                filtering: false,
                css: "created-column text-center",
                headerCss: "text-center",
                itemTemplate: function(value) {
                    return '<span class="small">' + value + '</span>';
                }
            },
            {
                type: "control",
                title: "Aksi",
                width: 120,
                css: "actions-column text-center",
                headerCss: "text-center",
                itemTemplate: function(value, item) {
                    return '<div class="btn-group btn-group-sm" role="group">' +
                           '<button type="button" class="btn btn-outline-info btn-sm" onclick="viewUser(' + item.id + ')" title="Lihat Detail">' +
                           '<i class="fas fa-eye"></i>' +
                           '</button>' +
                           '<button type="button" class="btn btn-outline-primary btn-sm" onclick="editUser(' + item.id + ')" title="Edit Pengguna">' +
                           '<i class="fas fa-edit"></i>' +
                           '</button>' +
                           '<button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteUser(' + item.id + ')" title="Hapus Pengguna">' +
                           '<i class="fas fa-trash"></i>' +
                           '</button>' +
                           '</div>';
                }
            }
        ]
    });

    // Form submission
    $('#userForm').on('submit', function(e) {
        e.preventDefault();

        // Clear previous validation errors
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        const formData = new FormData(this);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        if (isEditMode) {
            formData.append('_method', 'PUT');
        }

        const url = isEditMode
            ? `{{ route('admin.users.index') }}/${$('#userId').val()}`
            : "{{ route('admin.users.store') }}";

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Notiflix.Notify.success(response.message);
                    $('#userModal').modal('hide');
                    $("#jsGrid").jsGrid("loadData");
                    resetForm();
                }
            },
            error: function(xhr) {
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
});

// Open create modal
function openCreateModal() {
    isEditMode = false;
    resetForm();
    $('#modalTitle').text('Tambah Pengguna');
    $('#submitBtn').text('Simpan Pengguna');
    $('#password').attr('required', true);
    $('#password_confirmation').attr('required', true);
    $('#passwordRequired').show();
    $('#confirmRequired').show();
    $('#passwordHelp').text('Minimal 8 karakter');
    $('#userModal').modal('show');
}

// View user details
function viewUser(id) {
    $.ajax({
        url: `{{ route('admin.users.index') }}/${id}`,
        type: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                const user = response.data;
                const roleColors = {
                    'owner': 'danger',
                    'admin': 'warning',
                    'customer': 'primary'
                };

                $('#viewUserContent').html(`
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Nama:</strong><br>
                            ${user.name}
                        </div>
                        <div class="col-md-6">
                            <strong>Email:</strong><br>
                            ${user.email}
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Peran:</strong><br>
                            <span class="badge bg-${roleColors[user.role] || 'secondary'}">${user.role.charAt(0).toUpperCase() + user.role.slice(1)}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Email Terverifikasi:</strong><br>
                            ${user.email_verified_at ? '<span class="badge bg-success">Terverifikasi</span>' : '<span class="badge bg-warning">Belum Terverifikasi</span>'}
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Dibuat:</strong><br>
                            ${user.created_at}
                        </div>
                        <div class="col-md-6">
                            <strong>Diperbarui:</strong><br>
                            ${user.updated_at}
                        </div>
                    </div>
                `);
                $('#viewUserModal').modal('show');
            }
        },
        error: function(xhr) {
            Notiflix.Notify.failure('Gagal memuat detail pengguna');
        }
    });
}

// Edit user
function editUser(id) {
    $.ajax({
        url: `{{ route('admin.users.index') }}/${id}`,
        type: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                const user = response.data;
                isEditMode = true;

                $('#userId').val(user.id);
                $('#name').val(user.name);
                $('#email').val(user.email);
                $('#role').val(user.role);
                $('#email_verified').prop('checked', user.email_verified_at !== null);

                $('#modalTitle').text('Edit Pengguna');
                $('#submitBtn').text('Perbarui Pengguna');
                $('#password').removeAttr('required');
                $('#password_confirmation').removeAttr('required');
                $('#passwordRequired').hide();
                $('#confirmRequired').hide();
                $('#passwordHelp').text('Kosongkan untuk mempertahankan kata sandi saat ini');

                $('#userModal').modal('show');
            }
        },
        error: function(xhr) {
            Notiflix.Notify.failure('Gagal memuat data pengguna');
        }
    });
}

// Delete user
function deleteUser(id) {
    Notiflix.Confirm.show(
        'Konfirmasi Hapus',
        'Apakah Anda yakin ingin menghapus pengguna ini? Tindakan ini tidak dapat dibatalkan.',
        'Ya, Hapus',
        'Batal',
        function() {
            $.ajax({
                url: `{{ route('admin.users.index') }}/${id}`,
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
                    Notiflix.Notify.failure(xhr.responseJSON?.message || 'Gagal menghapus pengguna');
                }
            });
        },
        function() {
            // Cancel callback
        }
    );
}

// Reset form
function resetForm() {
    $('#userForm')[0].reset();
    $('#userId').val('');
    $('.form-control').removeClass('is-invalid');
    $('.invalid-feedback').text('');
}
</script>
@endpush