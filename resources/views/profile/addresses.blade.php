@extends('layouts.app')

@section('title', 'My Addresses - Fashion Marketplace')

@section('sidebar')
@php
    $role = auth()->user()->role;
@endphp

@if($role === 'owner')
    <li class="nav-item">
        <a href="{{ route('owner.dashboard') }}" class="nav-link">
            <i class="fas fa-home"></i>
            <p>Dashboard</p>
        </a>
    </li>
    <li class="nav-item">
        <a data-bs-toggle="collapse" href="#products">
            <i class="fas fa-tshirt"></i>
            <p>Product Management</p>
            <span class="caret"></span>
        </a>
        <div class="collapse" id="products">
            <ul class="nav nav-collapse">
                <li><a href="#"><span class="sub-item">All Products</span></a></li>
                <li><a href="#"><span class="sub-item">Add Product</span></a></li>
                <li><a href="#"><span class="sub-item">Categories</span></a></li>
            </ul>
        </div>
    </li>
    <li class="nav-item">
        <a data-bs-toggle="collapse" href="#users">
            <i class="fas fa-users"></i>
            <p>User Management</p>
            <span class="caret"></span>
        </a>
        <div class="collapse" id="users">
            <ul class="nav nav-collapse">
                <li><a href="#"><span class="sub-item">All Users</span></a></li>
                <li><a href="#"><span class="sub-item">Admins</span></a></li>
                <li><a href="#"><span class="sub-item">Customers</span></a></li>
            </ul>
        </div>
    </li>
    <li class="nav-item">
        <a href="#">
            <i class="fas fa-chart-bar"></i>
            <p>Reports</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="#">
            <i class="fas fa-cog"></i>
            <p>Settings</p>
        </a>
    </li>
@elseif($role === 'admin')
    <li class="nav-item">
        <a href="{{ route('admin.dashboard') }}" class="nav-link">
            <i class="fas fa-home"></i>
            <p>Dashboard</p>
        </a>
    </li>
    <li class="nav-item">
        <a data-bs-toggle="collapse" href="#products">
            <i class="fas fa-tshirt"></i>
            <p>Product Management</p>
            <span class="caret"></span>
        </a>
        <div class="collapse" id="products">
            <ul class="nav nav-collapse">
                <li><a href="#"><span class="sub-item">All Products</span></a></li>
                <li><a href="#"><span class="sub-item">Add Product</span></a></li>
                <li><a href="#"><span class="sub-item">Categories</span></a></li>
            </ul>
        </div>
    </li>
    <li class="nav-item">
        <a data-bs-toggle="collapse" href="#orders">
            <i class="fas fa-shopping-cart"></i>
            <p>Order Management</p>
            <span class="caret"></span>
        </a>
        <div class="collapse" id="orders">
            <ul class="nav nav-collapse">
                <li><a href="#"><span class="sub-item">All Orders</span></a></li>
                <li><a href="#"><span class="sub-item">Pending</span></a></li>
                <li><a href="#"><span class="sub-item">Completed</span></a></li>
            </ul>
        </div>
    </li>
    <li class="nav-item">
        <a href="#">
            <i class="fas fa-users"></i>
            <p>Customer Management</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="#">
            <i class="fas fa-chart-line"></i>
            <p>Analytics</p>
        </a>
    </li>
@else
    <li class="nav-item">
        <a href="{{ route('customer.dashboard') }}" class="nav-link">
            <i class="fas fa-home"></i>
            <p>Dashboard</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="#">
            <i class="fas fa-shopping-bag"></i>
            <p>Browse Products</p>
        </a>
    </li>
    <li class="nav-item">
        <a data-bs-toggle="collapse" href="#orders">
            <i class="fas fa-shopping-cart"></i>
            <p>My Orders</p>
            <span class="caret"></span>
        </a>
        <div class="collapse" id="orders">
            <ul class="nav nav-collapse">
                <li><a href="#"><span class="sub-item">All Orders</span></a></li>
                <li><a href="#"><span class="sub-item">Pending</span></a></li>
                <li><a href="#"><span class="sub-item">Completed</span></a></li>
            </ul>
        </div>
    </li>
    <li class="nav-item">
        <a href="#">
            <i class="fas fa-heart"></i>
            <p>Wishlist</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="#">
            <i class="fas fa-credit-card"></i>
            <p>Payment Methods</p>
        </a>
    </li>
@endif

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('notiflix-Notiflix-67ba12d/dist/notiflix-3.2.7.min.css') }}">
<style>
.address-card {
    transition: all 0.3s ease;
    border: 2px solid transparent;
}
.address-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.address-card.default {
    border-color: #059669;
    background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
}
.default-badge {
    background: linear-gradient(135deg, #059669, #047857);
}
.modal {
    backdrop-filter: blur(5px);
}
.form-control:focus {
    border-color: #059669;
    box-shadow: 0 0 0 0.2rem rgba(5, 150, 105, 0.25);
}
</style>
@endpush

@section('content')
<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    <div>
        <h3 class="fw-bold mb-3">My Addresses</h3>
        <h6 class="op-7 mb-2">Manage your shipping addresses for orders</h6>
    </div>
    <div class="ms-md-auto py-2 py-md-0">
        <button onclick="openAddModal()" class="btn btn-primary btn-round">
            <i class="fas fa-plus me-2"></i>Add New Address
        </button>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">Address Management</div>
                    <div class="card-tools">
                        @if($addresses->count() > 0)
                            <span class="badge badge-info">{{ $addresses->count() }} {{ Str::plural('Address', $addresses->count()) }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="addresses-grid" class="row">
                    @forelse($addresses as $address)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card {{ $address->is_default ? 'card-light' : '' }} address-card" data-id="{{ $address->id }}">
                                <div class="card-body">
                                    <!-- Default Badge -->
                                    @if($address->is_default)
                                        <div class="float-end">
                                            <span class="badge badge-success">
                                                <i class="fas fa-check me-1"></i>Default
                                            </span>
                                        </div>
                                    @endif

                                    <!-- Address Info -->
                                    <div class="info-user ms-1">
                                        <div class="username">{{ $address->label }}</div>
                                        <div class="status">{{ $address->recipient_name }}</div>
                                        <div class="status text-muted">{{ $address->recipient_phone }}</div>
                                    </div>

                                    <div class="separator-dashed my-3"></div>

                                    <div class="mb-3">
                                        <small class="text-muted">
                                            {{ $address->address_detail }}<br>
                                            {{ $address->village->name ?? '' }}, {{ $address->district->name ?? '' }}<br>
                                            {{ $address->city->name ?? '' }}, {{ $address->province->name ?? '' }}
                                            @if($address->postal_code)
                                                <br>{{ $address->postal_code }}
                                            @endif
                                        </small>
                                    </div>

                                    @if($address->notes)
                                        <div class="mb-3">
                                            <div class="alert alert-light py-2">
                                                <small class="text-muted fst-italic">
                                                    <i class="fas fa-sticky-note me-1"></i>
                                                    "{{ $address->notes }}"
                                                </small>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Actions -->
                                    <div class="d-flex gap-2">
                                        <button onclick="viewAddress({{ $address->id }})" class="btn btn-info btn-sm" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        <button onclick="editAddress({{ $address->id }})" class="btn btn-warning btn-sm" title="Edit Address">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        @unless($address->is_default)
                                            <button onclick="makeDefault({{ $address->id }})" class="btn btn-success btn-sm" title="Make Default">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endunless

                                        <button onclick="deleteAddress({{ $address->id }}, '{{ $address->label }}')" class="btn btn-danger btn-sm" title="Delete Address">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="text-center py-5">
                                <div class="empty-state" data-height="400">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-map-marker-alt" style="font-size: 4rem; color: #6c757d;"></i>
                                    </div>
                                    <h2>No addresses yet</h2>
                                    <p class="lead">Get started by adding your first shipping address.</p>
                                    <button onclick="openAddModal()" class="btn btn-primary btn-lg">
                                        <i class="fas fa-plus me-2"></i>Add Your First Address
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Address Modal -->
<div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalTitle">Add New Address</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addressForm">
            @csrf
            <input type="hidden" id="addressId" name="address_id">
            <input type="hidden" id="formMethod" name="_method" value="POST">

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="form-label">Address Label *</label>
                                <input type="text" name="label" id="label" class="form-control" placeholder="e.g. Home, Office" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">&nbsp;</label>
                                <div class="form-check">
                                    <input type="checkbox" name="is_default" id="is_default" class="form-check-input">
                                    <label class="form-check-label" for="is_default">Set as default</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Recipient Name *</label>
                                <input type="text" name="recipient_name" id="recipient_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Phone Number *</label>
                                <input type="text" name="recipient_phone" id="recipient_phone" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Province *</label>
                                <select name="province_id" id="province_id" class="form-select" required>
                                    <option value="">Select Province</option>
                                    @foreach($provinces as $province)
                                        <option value="{{ $province->id }}">{{ $province->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">City/Regency *</label>
                                <select name="city_id" id="city_id" class="form-select" required disabled>
                                    <option value="">Select City</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">District *</label>
                                <select name="district_id" id="district_id" class="form-select" required disabled>
                                    <option value="">Select District</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Village *</label>
                                <select name="village_id" id="village_id" class="form-select" required disabled>
                                    <option value="">Select Village</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Complete Address *</label>
                        <textarea name="address_detail" id="address_detail" rows="3" class="form-control" placeholder="Street, building number, RT/RW, etc." required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Postal Code</label>
                                <input type="text" name="postal_code" id="postal_code" class="form-control" placeholder="e.g. 12345">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Notes</label>
                                <input type="text" name="notes" id="notes" class="form-control" placeholder="Additional notes">
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="addressForm" id="submitBtn" class="btn btn-primary">
                    <span id="loadingIcon" class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
                    <span id="submitText">Save Address</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Address Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="viewModalTitle">Address Details</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewModalContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning" onclick="editFromView()">
                    <i class="fas fa-edit me-1"></i>Edit Address
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('notiflix-Notiflix-67ba12d/dist/notiflix-3.2.7.min.js') }}"></script>
<script>
// Configure Notiflix
Notiflix.Notify.init({
    width: '300px',
    position: 'right-top',
    distance: '20px',
    opacity: 1,
    borderRadius: '8px',
    rtl: false,
    timeout: 4000,
    messageMaxLength: 110,
    showOnlyTheLastOne: true,
    clickToClose: true,
    pauseOnHover: true,

    success: {
        background: '#059669',
        textColor: '#fff',
        childClassName: 'notiflix-notify-success',
        notiflixIconColor: 'rgba(255,255,255,0.9)',
    },

    failure: {
        background: '#DC2626',
        textColor: '#fff',
        childClassName: 'notiflix-notify-failure',
        notiflixIconColor: 'rgba(255,255,255,0.9)',
    },

    info: {
        background: '#2563EB',
        textColor: '#fff',
        childClassName: 'notiflix-notify-info',
        notiflixIconColor: 'rgba(255,255,255,0.9)',
    },

    warning: {
        background: '#D97706',
        textColor: '#fff',
        childClassName: 'notiflix-notify-warning',
        notiflixIconColor: 'rgba(255,255,255,0.9)',
    },
});

Notiflix.Confirm.init({
    width: '320px',
    backgroundColor: '#fff',
    titleColor: '#1F2937',
    messageColor: '#374151',
    buttonsFontSize: '15px',
    buttonsMaxLength: 34,
    okButtonBackground: '#DC2626',
    okButtonColor: '#fff',
    cancelButtonBackground: '#6B7280',
    cancelButtonColor: '#fff',
    distance: '20px',
    borderRadius: '8px'
});

// Location cascade functionality
let isEditMode = false;

// Province change handler
document.getElementById('province_id').addEventListener('change', function() {
    const provinceId = this.value;
    const citySelect = document.getElementById('city_id');
    const districtSelect = document.getElementById('district_id');
    const villageSelect = document.getElementById('village_id');

    // Reset dependent selects
    citySelect.innerHTML = '<option value="">Select City</option>';
    districtSelect.innerHTML = '<option value="">Select District</option>';
    villageSelect.innerHTML = '<option value="">Select Village</option>';

    citySelect.disabled = true;
    districtSelect.disabled = true;
    villageSelect.disabled = true;

    if (provinceId) {
        fetch(`/api/cities/public?province_id=${provinceId}`)
            .then(response => response.json())
            .then(cities => {
                cities.forEach(city => {
                    citySelect.innerHTML += `<option value="${city.id}">${city.name}</option>`;
                });
                citySelect.disabled = false;
            })
            .catch(error => {
                console.error('Error loading cities:', error);
                Notiflix.Notify.failure('Failed to load cities');
            });
    }
});

// City change handler
document.getElementById('city_id').addEventListener('change', function() {
    const cityId = this.value;
    const districtSelect = document.getElementById('district_id');
    const villageSelect = document.getElementById('village_id');

    // Reset dependent selects
    districtSelect.innerHTML = '<option value="">Select District</option>';
    villageSelect.innerHTML = '<option value="">Select Village</option>';

    districtSelect.disabled = true;
    villageSelect.disabled = true;

    if (cityId) {
        fetch(`/api/districts/public?city_id=${cityId}`)
            .then(response => response.json())
            .then(districts => {
                districts.forEach(district => {
                    districtSelect.innerHTML += `<option value="${district.id}">${district.name}</option>`;
                });
                districtSelect.disabled = false;
            })
            .catch(error => {
                console.error('Error loading districts:', error);
                Notiflix.Notify.failure('Failed to load districts');
            });
    }
});

// District change handler
document.getElementById('district_id').addEventListener('change', function() {
    const districtId = this.value;
    const villageSelect = document.getElementById('village_id');

    // Reset dependent select
    villageSelect.innerHTML = '<option value="">Select Village</option>';
    villageSelect.disabled = true;

    if (districtId) {
        fetch(`/api/villages/public?district_id=${districtId}`)
            .then(response => response.json())
            .then(villages => {
                villages.forEach(village => {
                    villageSelect.innerHTML += `<option value="${village.id}">${village.name}</option>`;
                });
                villageSelect.disabled = false;
            })
            .catch(error => {
                console.error('Error loading villages:', error);
                Notiflix.Notify.failure('Failed to load villages');
            });
    }
});

// Modal functions
function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Add New Address';
    document.getElementById('addressForm').reset();
    document.getElementById('addressId').value = '';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('submitText').textContent = 'Save Address';

    // Reset location selects
    ['city_id', 'district_id', 'village_id'].forEach(id => {
        const select = document.getElementById(id);
        select.innerHTML = `<option value="">Select ${id.replace('_id', '').replace('_', ' ')}</option>`;
        select.disabled = true;
    });

    isEditMode = false;
    const modal = new bootstrap.Modal(document.getElementById('addressModal'));
    modal.show();
}

function closeModal() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('addressModal'));
    if (modal) modal.hide();
}

function viewAddress(addressId) {
    fetch(`/addresses/${addressId}`)
        .then(response => response.json())
        .then(data => {
            const address = data.address;
            const content = `
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="mb-3">${address.label}</h5>
                        <div class="mb-2">
                            <strong>Recipient:</strong><br>
                            ${address.recipient_name}<br>
                            <small class="text-muted">${address.recipient_phone}</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        ${address.is_default ? '<span class="badge badge-success float-end"><i class="fas fa-check me-1"></i>Default Address</span>' : ''}
                    </div>
                </div>

                <hr>

                <div class="mb-3">
                    <strong>Complete Address:</strong><br>
                    <p class="mb-1">${address.address_detail}</p>
                    <small class="text-muted">
                        ${address.village?.name || ''}, ${address.district?.name || ''}<br>
                        ${address.city?.name || ''}, ${address.province?.name || ''}
                        ${address.postal_code ? '<br>' + address.postal_code : ''}
                    </small>
                </div>

                ${address.notes ? `
                <div class="mb-3">
                    <strong>Notes:</strong><br>
                    <div class="alert alert-light py-2">
                        <small class="text-muted fst-italic">
                            <i class="fas fa-sticky-note me-1"></i>
                            "${address.notes}"
                        </small>
                    </div>
                </div>
                ` : ''}

                ${address.latitude && address.longitude ? `
                <div class="mb-3">
                    <strong>Coordinates:</strong><br>
                    <small class="text-muted">
                        Lat: ${address.latitude}, Lng: ${address.longitude}
                    </small>
                </div>
                ` : ''}
            `;

            document.getElementById('viewModalContent').innerHTML = content;
            document.getElementById('viewModalTitle').textContent = `${address.label} - Details`;

            // Store address ID for edit function
            document.getElementById('viewModal').setAttribute('data-address-id', addressId);

            const modal = new bootstrap.Modal(document.getElementById('viewModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error loading address:', error);
            Notiflix.Notify.failure('Failed to load address details');
        });
}

function editFromView() {
    const addressId = document.getElementById('viewModal').getAttribute('data-address-id');
    const viewModal = bootstrap.Modal.getInstance(document.getElementById('viewModal'));
    if (viewModal) viewModal.hide();

    // Wait for view modal to close, then open edit modal
    setTimeout(() => {
        editAddress(addressId);
    }, 300);
}

function editAddress(addressId) {
    document.getElementById('modalTitle').textContent = 'Edit Address';
    document.getElementById('addressId').value = addressId;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('submitText').textContent = 'Update Address';

    isEditMode = true;

    // Load address data
    fetch(`/addresses/${addressId}/edit`)
        .then(response => response.json())
        .then(data => {
            const address = data.address;

            // Fill form fields
            document.getElementById('label').value = address.label || '';
            document.getElementById('recipient_name').value = address.recipient_name || '';
            document.getElementById('recipient_phone').value = address.recipient_phone || '';
            document.getElementById('address_detail').value = address.address_detail || '';
            document.getElementById('postal_code').value = address.postal_code || '';
            document.getElementById('notes').value = address.notes || '';
            document.getElementById('is_default').checked = address.is_default || false;

            // Set province and load cascade
            document.getElementById('province_id').value = address.province_id;
            loadCitiesForEdit(address.province_id, data.cities, address.city_id, () => {
                loadDistrictsForEdit(address.city_id, data.districts, address.district_id, () => {
                    loadVillagesForEdit(address.district_id, data.villages, address.village_id);
                });
            });

            const modal = new bootstrap.Modal(document.getElementById('addressModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error loading address:', error);
            Notiflix.Notify.failure('Failed to load address data');
        });
}

function loadCitiesForEdit(provinceId, cities, selectedCityId, callback) {
    const citySelect = document.getElementById('city_id');
    citySelect.innerHTML = '<option value="">Select City</option>';

    cities.forEach(city => {
        citySelect.innerHTML += `<option value="${city.id}" ${city.id == selectedCityId ? 'selected' : ''}>${city.name}</option>`;
    });

    citySelect.disabled = false;
    if (callback) callback();
}

function loadDistrictsForEdit(cityId, districts, selectedDistrictId, callback) {
    const districtSelect = document.getElementById('district_id');
    districtSelect.innerHTML = '<option value="">Select District</option>';

    districts.forEach(district => {
        districtSelect.innerHTML += `<option value="${district.id}" ${district.id == selectedDistrictId ? 'selected' : ''}>${district.name}</option>`;
    });

    districtSelect.disabled = false;
    if (callback) callback();
}

function loadVillagesForEdit(districtId, villages, selectedVillageId) {
    const villageSelect = document.getElementById('village_id');
    villageSelect.innerHTML = '<option value="">Select Village</option>';

    villages.forEach(village => {
        villageSelect.innerHTML += `<option value="${village.id}" ${village.id == selectedVillageId ? 'selected' : ''}>${village.name}</option>`;
    });

    villageSelect.disabled = false;
}

// Form submission
document.getElementById('addressForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const addressId = document.getElementById('addressId').value;
    const method = document.getElementById('formMethod').value;

    // Show loading state
    document.getElementById('loadingIcon').classList.remove('d-none');
    document.getElementById('submitBtn').disabled = true;

    let url = '/addresses';
    if (method === 'PUT') {
        url = `/addresses/${addressId}`;
        formData.append('_method', 'PUT');
    }

    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Notiflix.Notify.success(data.message);
            closeModal();
            location.reload(); // Reload to show updated data
        } else {
            throw new Error(data.message || 'Something went wrong');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Notiflix.Notify.failure(error.message || 'Failed to save address');
    })
    .finally(() => {
        document.getElementById('loadingIcon').classList.add('d-none');
        document.getElementById('submitBtn').disabled = false;
    });
});

// Make address default
function makeDefault(addressId) {
    fetch(`/addresses/${addressId}/default`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Notiflix.Notify.success(data.message);
            location.reload();
        } else {
            throw new Error(data.message || 'Failed to update default address');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Notiflix.Notify.failure(error.message || 'Failed to update default address');
    });
}

// Delete address
function deleteAddress(addressId, label) {
    Notiflix.Confirm.show(
        'Delete Address',
        `Are you sure you want to delete "${label}"? This action cannot be undone.`,
        'Yes, Delete',
        'Cancel',
        function () {
            // User clicked Yes
            fetch(`/addresses/${addressId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Notiflix.Notify.success(data.message);
                    location.reload();
                } else {
                    throw new Error(data.message || 'Failed to delete address');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Notiflix.Notify.failure(error.message || 'Failed to delete address');
            });
        },
        function () {
            // User clicked Cancel - do nothing
        }
    );
}

// Bootstrap modals handle escape key and backdrop click automatically
</script>
@endpush