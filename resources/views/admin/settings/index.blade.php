@extends('layouts.app')

@section('title', 'Pengaturan Sistem - Dashboard Admin')


@section('content')
<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    <div>
        <h3 class="fw-bold mb-3">Pengaturan Sistem</h3>
        <h6 class="op-7 mb-2">Konfigurasi pengaturan sistem untuk marketplace Anda</h6>
    </div>
    <div class="ms-md-auto py-2 py-md-0">
        <button class="btn btn-primary btn-round" onclick="syncAllAPIs()">
            <i class="fas fa-sync-alt me-2"></i>Sinkronkan Semua API
        </button>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>Berhasil!</strong> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>Error!</strong> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<form id="settingsForm" action="{{ route('admin.settings.update') }}" method="POST">
    @csrf
    @method('PUT')

    <!-- General Settings -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-round">
                <div class="card-header">
                    <div class="card-head-row">
                        <div class="card-title">
                            <i class="fas fa-info-circle me-2"></i>Pengaturan Umum
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(isset($settings['general']))
                        <div class="row">
                            @foreach($settings['general'] as $setting)
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="{{ $setting->key }}" class="form-label">
                                            {{ ucfirst(str_replace('_', ' ', $setting->key)) }}
                                            @if($setting->is_public)
                                                <span class="badge badge-info ms-2">Public</span>
                                            @endif
                                        </label>
                                        @if($setting->type === 'json')
                                            <textarea name="settings[{{ $setting->key }}]" id="{{ $setting->key }}"
                                                    class="form-control" rows="3"
                                                    placeholder="{{ $setting->description }}">@php
                                                        $jsonValue = json_decode($setting->value, true);
                                                        if (json_last_error() === JSON_ERROR_NONE && is_array($jsonValue)) {
                                                            echo json_encode($jsonValue, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                                                        } else {
                                                            echo $setting->value;
                                                        }
                                                    @endphp</textarea>
                                        @else
                                            <input type="{{ $setting->type === 'number' ? 'number' : 'text' }}"
                                                   name="settings[{{ $setting->key }}]"
                                                   id="{{ $setting->key }}"
                                                   class="form-control @error('settings.' . $setting->key) is-invalid @enderror"
                                                   value="{{ old('settings.' . $setting->key, $setting->value) }}"
                                                   placeholder="{{ $setting->description }}">
                                        @endif
                                        @if($setting->description)
                                            <small class="form-text text-muted">{{ $setting->description }}</small>
                                        @endif
                                        @error('settings.' . $setting->key)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>


    <!-- Shipping Settings -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-round">
                <div class="card-header">
                    <div class="card-head-row">
                        <div class="card-title">
                            <i class="fas fa-shipping-fast me-2"></i>Pengaturan Pengiriman (RajaOngkir)
                        </div>
                        <div class="card-tools">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="syncRajaOngkir()">
                                <i class="fas fa-sync-alt me-1"></i>Sinkronkan Data
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="rajaongkir-status" class="mb-3" style="display: none;"></div>

                    <!-- Current Shipping Origin Display -->
                    @if($shippingOrigin['province'] || $shippingOrigin['city'] || $shippingOrigin['district'])
                    <div class="alert alert-info mb-4">
                        <h6 class="alert-heading mb-2"><i class="fas fa-map-marker-alt me-2"></i>Asal Pengiriman Saat Ini</h6>
                        <div class="mb-1">
                            <strong>Lokasi:</strong>
                            {{ $shippingOrigin['district']['name'] ?? 'Belum Diatur' }},
                            {{ $shippingOrigin['city']['name'] ?? 'Belum Diatur' }},
                            {{ $shippingOrigin['province']['name'] ?? 'Belum Diatur' }}
                        </div>
                        @if($shippingOrigin['address'])
                        <div class="mb-1"><strong>Alamat:</strong> {{ $shippingOrigin['address'] }}</div>
                        @endif
                    </div>
                    @endif

                    <!-- Location Picker Section -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-map-marked-alt me-2"></i>Lokasi Asal Pengiriman
                            <span class="badge badge-warning ms-2">Private</span>
                        </label>
                        <button type="button" class="btn btn-outline-primary btn-sm ms-2" onclick="showLocationPicker()">
                            <i class="fas fa-edit me-1"></i>Ubah Lokasi
                        </button>
                    </div>

                    @if(isset($settings['shipping']))
                        @foreach($settings['shipping'] as $setting)
                            @if(!in_array($setting->key, ['shipping_origin_province_id', 'shipping_origin_city_id', 'shipping_origin_district_id']))
                            <div class="form-group">
                                <label for="{{ $setting->key }}" class="form-label">
                                    {{ ucfirst(str_replace(['_', 'rajaongkir'], [' ', 'RajaOngkir'], $setting->key)) }}
                                    @if(!$setting->is_public)
                                        <span class="badge badge-warning ms-2">Private</span>
                                    @endif
                                </label>
                                @if($setting->type === 'json')
                                    <textarea name="settings[{{ $setting->key }}]" id="{{ $setting->key }}"
                                            class="form-control" rows="3"
                                            placeholder="{{ $setting->description }}">@php
                                                $jsonValue = json_decode($setting->value, true);
                                                if (json_last_error() === JSON_ERROR_NONE && is_array($jsonValue)) {
                                                    echo json_encode($jsonValue, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                                                } else {
                                                    echo $setting->value;
                                                }
                                            @endphp</textarea>
                                @else
                                    <input type="{{ $setting->key === 'rajaongkir_api_key' ? 'password' : ($setting->type === 'number' ? 'number' : 'text') }}"
                                           name="settings[{{ $setting->key }}]"
                                           id="{{ $setting->key }}"
                                           class="form-control @error('settings.' . $setting->key) is-invalid @enderror"
                                           value="{{ old('settings.' . $setting->key, $setting->value) }}"
                                           placeholder="{{ $setting->description }}"
                                           @if($setting->type === 'number') step="1" min="0" @endif
                                           @if($setting->key === 'rajaongkir_origin_city_id') readonly @endif
                                           @if(in_array($setting->key, ['rajaongkir_api_key', 'rajaongkir_origin_city_id'])) data-optional="true" @endif>
                                @endif
                                @if($setting->description)
                                    <small class="form-text text-muted">
                                        {{ $setting->description }}
                                        @if(in_array($setting->key, ['rajaongkir_api_key', 'rajaongkir_origin_city_id']))
                                            <br><em class="text-secondary">Optional - can be configured later</em>
                                        @endif
                                    </small>
                                @endif
                                @error('settings.' . $setting->key)
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            @endif
                        @endforeach
                    @endif

                    <!-- Hidden inputs for location IDs -->
                    <input type="hidden" name="settings[shipping_origin_province_id]" id="hidden_province_id" value="{{ $shippingOrigin['province']['id'] ?? '' }}">
                    <input type="hidden" name="settings[shipping_origin_city_id]" id="hidden_city_id" value="{{ $shippingOrigin['city']['id'] ?? '' }}">
                    <input type="hidden" name="settings[shipping_origin_district_id]" id="hidden_district_id" value="{{ $shippingOrigin['district']['id'] ?? '' }}">
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Settings -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-round">
                <div class="card-header">
                    <div class="card-head-row">
                        <div class="card-title">
                            <i class="fas fa-credit-card me-2"></i>Pengaturan Pembayaran (Midtrans)
                        </div>
                        <div class="card-tools">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="syncMidtrans()">
                                <i class="fas fa-sync-alt me-1"></i>Sinkronkan Pengaturan
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="midtrans-status" class="mb-3" style="display: none;"></div>
                    @if(isset($settings['payment']))
                        @foreach($settings['payment'] as $setting)
                            <div class="form-group">
                                <label for="{{ $setting->key }}" class="form-label">
                                    {{ ucfirst(str_replace(['_', 'midtrans'], [' ', 'Midtrans'], $setting->key)) }}
                                    @if(!$setting->is_public)
                                        <span class="badge badge-warning ms-2">Private</span>
                                    @endif
                                </label>
                                @if($setting->key === 'midtrans_environment')
                                    <select name="settings[{{ $setting->key }}]" id="{{ $setting->key }}" class="form-control">
                                        <option value="sandbox" {{ $setting->value === 'sandbox' ? 'selected' : '' }}>Sandbox</option>
                                        <option value="production" {{ $setting->value === 'production' ? 'selected' : '' }}>Production</option>
                                    </select>
                                @elseif($setting->type === 'json')
                                    <textarea name="settings[{{ $setting->key }}]" id="{{ $setting->key }}"
                                            class="form-control" rows="6"
                                            placeholder="{{ $setting->description }}">@php
                                                $jsonValue = json_decode($setting->value, true);
                                                if (json_last_error() === JSON_ERROR_NONE && is_array($jsonValue)) {
                                                    echo json_encode($jsonValue, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                                                } else {
                                                    echo $setting->value;
                                                }
                                            @endphp</textarea>
                                @else
                                    <input type="{{ in_array($setting->key, ['midtrans_server_key', 'midtrans_client_key']) ? 'password' : 'text' }}"
                                           name="settings[{{ $setting->key }}]"
                                           id="{{ $setting->key }}"
                                           class="form-control @error('settings.' . $setting->key) is-invalid @enderror"
                                           value="{{ old('settings.' . $setting->key, $setting->value) }}"
                                           placeholder="{{ $setting->description }}"
                                           @if(in_array($setting->key, ['midtrans_server_key', 'midtrans_client_key', 'midtrans_merchant_id'])) data-optional="true" @endif>
                                @endif
                                @if($setting->description)
                                    <small class="form-text text-muted">
                                        {{ $setting->description }}
                                        @if(in_array($setting->key, ['midtrans_server_key', 'midtrans_client_key', 'midtrans_merchant_id']))
                                            <br><em class="text-secondary">Optional - can be configured later</em>
                                        @endif
                                    </small>
                                @endif
                                @error('settings.' . $setting->key)
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card card-round">
                <div class="card-body text-center">
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="fas fa-save me-2"></i>Simpan Semua Pengaturan
                    </button>
                    <button type="button" class="btn btn-secondary btn-lg px-5 ms-3" onclick="confirmResetChanges()">
                        <i class="fas fa-undo me-2"></i>Reset Perubahan
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Location Picker Modal -->
<div class="modal fade" id="locationPickerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-map-marked-alt me-2"></i>Pilih Lokasi Asal Pengiriman
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="locationForm">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="modal_province" class="form-label">Provinsi</label>
                                <select id="modal_province" class="form-control" onchange="loadCities()">
                                    <option value="">Pilih Provinsi</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="modal_city" class="form-label">Kota</label>
                                <select id="modal_city" class="form-control" onchange="loadDistricts()" disabled>
                                    <option value="">Pilih Kota</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="modal_district" class="form-label">Kecamatan</label>
                                <select id="modal_district" class="form-control" disabled>
                                    <option value="">Pilih Kecamatan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="modal_address" class="form-label">Alamat Lengkap</label>
                        <textarea id="modal_address" class="form-control" rows="3"
                                placeholder="Masukkan detail alamat lengkap"></textarea>
                    </div>
                    <div class="alert alert-info mt-3" id="rajaongkir-info" style="display: none;">
                        <i class="fas fa-info-circle me-2"></i>
                        <span id="rajaongkir-message"></span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="saveLocation()" disabled id="saveLocationBtn">
                    <i class="fas fa-save me-2"></i>Simpan Lokasi
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Loading handled by Notiflix -->
@endsection

@push('scripts')
<script>
// Location Picker Functions
let provinces = [];
let cities = [];
let districts = [];

function showLocationPicker() {
    const modal = new bootstrap.Modal(document.getElementById('locationPickerModal'));
    loadProvinces();

    // Pre-fill current values if available
    const currentAddress = @json($shippingOrigin['address'] ?? '');
    document.getElementById('modal_address').value = currentAddress;

    modal.show();
}

async function loadProvinces() {
    try {
        const response = await fetch('/api/provinces/public');
        if (!response.ok) {
            // Fallback: load from existing data if API doesn't exist yet
            provinces = @json(\App\Models\Province::all(['id', 'name', 'rajaongkir_id']));
        } else {
            provinces = await response.json();
        }

        const select = document.getElementById('modal_province');
        select.innerHTML = '<option value="">Select Province</option>';

        provinces.forEach(province => {
            const option = document.createElement('option');
            option.value = province.id;
            option.textContent = province.name;
            option.dataset.rajaongkirId = province.rajaongkir_id || '';
            select.appendChild(option);
        });

        // Pre-select current province
        const currentProvinceId = @json($shippingOrigin['province']['id'] ?? '');
        if (currentProvinceId) {
            select.value = currentProvinceId;
            loadCities();
        }

    } catch (error) {
        console.error('Failed to load provinces:', error);
        Notiflix.Notify.failure('Failed to load provinces. Please refresh the page.');
    }
}

async function loadCities() {
    const provinceId = document.getElementById('modal_province').value;
    const citySelect = document.getElementById('modal_city');
    const districtSelect = document.getElementById('modal_district');

    // Reset dependent dropdowns
    citySelect.innerHTML = '<option value="">Select City</option>';
    citySelect.disabled = !provinceId;
    districtSelect.innerHTML = '<option value="">Select District</option>';
    districtSelect.disabled = true;

    updateSaveButton();

    if (!provinceId) return;

    try {
        const url = `/api/cities/public?province_id=${provinceId}`;
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error('Failed to fetch cities');
        }

        cities = await response.json();

        cities.forEach(city => {
            const option = document.createElement('option');
            option.value = city.id;
            option.textContent = city.name;
            option.dataset.rajaongkirId = city.rajaongkir_id || '';
            option.dataset.rajaongkirType = city.rajaongkir_type || '';
            citySelect.appendChild(option);
        });

        citySelect.disabled = false;

        // Pre-select current city
        const currentCityId = @json($shippingOrigin['city']['id'] ?? '');
        if (currentCityId && provinceId == @json($shippingOrigin['province']['id'] ?? '')) {
            citySelect.value = currentCityId;
            loadDistricts();
        }

    } catch (error) {
        console.error('Failed to load cities:', error);
        Notiflix.Notify.failure('Failed to load cities. Please try again.');
    }
}

async function loadDistricts() {
    const cityId = document.getElementById('modal_city').value;
    const districtSelect = document.getElementById('modal_district');

    console.log('loadDistricts called with cityId:', cityId); // Debug

    // Safety check
    if (!districtSelect) {
        console.error('District select element not found');
        return;
    }

    // Reset district dropdown
    districtSelect.innerHTML = '<option value="">Select District</option>';
    districtSelect.disabled = !cityId;

    updateSaveButton();
    updateRajaOngkirInfo();

    if (!cityId) {
        console.log('No cityId provided, skipping API call');
        return;
    }

    try {
        console.log('Loading districts for city ID:', cityId);
        const url = `/api/districts/public?city_id=${cityId}`;
        console.log('API URL:', url);

        const response = await fetch(url);
        console.log('Districts API response status:', response.status);

        if (!response.ok) {
            const errorText = await response.text();
            console.error('API Error Response:', errorText);
            throw new Error(`HTTP ${response.status}: ${errorText}`);
        }

        districts = await response.json();
        console.log('Districts response:', districts);

        if (!Array.isArray(districts)) {
            console.error('Districts response is not an array:', typeof districts);
            throw new Error('Invalid response format');
        }

        if (districts.length === 0) {
            console.log('No districts found for city ID:', cityId);
            districtSelect.innerHTML = '<option value="">No districts found for this city</option>';
            return;
        }

        console.log(`Loading ${districts.length} districts into dropdown`);

        districts.forEach((district, index) => {
            console.log(`Adding district ${index}:`, district);
            const option = document.createElement('option');
            option.value = district.id;
            option.textContent = district.name;
            districtSelect.appendChild(option);
        });

        districtSelect.disabled = false;
        console.log('Districts dropdown enabled with', districtSelect.options.length - 1, 'options');

        // Pre-select current district
        const currentDistrictId = @json($shippingOrigin['district']['id'] ?? '');
        if (currentDistrictId && cityId == @json($shippingOrigin['city']['id'] ?? '')) {
            console.log('Pre-selecting district:', currentDistrictId);
            districtSelect.value = currentDistrictId;
            updateSaveButton();
        }

    } catch (error) {
        console.error('Failed to load districts:', error);
        districtSelect.innerHTML = '<option value="">Error loading districts</option>';
        Notiflix.Notify.failure('Failed to load districts: ' + error.message);
    }
}

function updateRajaOngkirInfo() {
    const citySelect = document.getElementById('modal_city');
    const infoDiv = document.getElementById('rajaongkir-info');
    const messageSpan = document.getElementById('rajaongkir-message');

    // Safety checks for null elements
    if (!citySelect || !infoDiv || !messageSpan) {
        console.error('Required elements not found:', {
            citySelect: !!citySelect,
            infoDiv: !!infoDiv,
            messageSpan: !!messageSpan
        });
        return;
    }

    if (!citySelect.value) {
        infoDiv.style.display = 'none';
        return;
    }

    const selectedOption = citySelect.options[citySelect.selectedIndex];
    if (!selectedOption) {
        infoDiv.style.display = 'none';
        return;
    }

    const rajaongkirId = selectedOption.dataset.rajaongkirId;
    const rajaongkirType = selectedOption.dataset.rajaongkirType;

    if (rajaongkirId) {
        infoDiv.className = 'alert alert-success mt-3';
        messageSpan.innerHTML = `
            <strong>RajaOngkir Ready!</strong><br>
            City ID: ${rajaongkirId} (${rajaongkirType || 'City'})<br>
            This location supports automatic shipping calculation.
        `;
    } else {
        infoDiv.className = 'alert alert-warning mt-3';
        messageSpan.innerHTML = `
            <strong>RajaOngkir Not Available</strong><br>
            This city is not supported by RajaOngkir API.<br>
            Manual shipping calculation may be required.
        `;
    }

    infoDiv.style.display = 'block';
}

function updateSaveButton() {
    const provinceId = document.getElementById('modal_province').value;
    const cityId = document.getElementById('modal_city').value;
    const districtId = document.getElementById('modal_district').value;
    const saveBtn = document.getElementById('saveLocationBtn');

    const isValid = provinceId && cityId && districtId;
    saveBtn.disabled = !isValid;

    // Also update when district changes
    document.getElementById('modal_district').onchange = updateSaveButton;
}

function saveLocation() {
    const provinceId = document.getElementById('modal_province').value;
    const cityId = document.getElementById('modal_city').value;
    const districtId = document.getElementById('modal_district').value;
    const address = document.getElementById('modal_address').value;

    console.log('saveLocation called with:', { provinceId, cityId, districtId, address });

    if (!provinceId || !cityId || !districtId) {
        Notiflix.Notify.warning('Please select province, city, and district');
        return;
    }

    // Get location names for display
    const provinceName = document.getElementById('modal_province').options[document.getElementById('modal_province').selectedIndex].text;
    const cityName = document.getElementById('modal_city').options[document.getElementById('modal_city').selectedIndex].text;
    const districtName = document.getElementById('modal_district').options[document.getElementById('modal_district').selectedIndex].text;

    console.log('Location names:', { provinceName, cityName, districtName });

    // Update hidden form inputs
    const hiddenProvinceInput = document.getElementById('hidden_province_id');
    const hiddenCityInput = document.getElementById('hidden_city_id');
    const hiddenDistrictInput = document.getElementById('hidden_district_id');

    if (hiddenProvinceInput) hiddenProvinceInput.value = provinceId;
    if (hiddenCityInput) hiddenCityInput.value = cityId;
    if (hiddenDistrictInput) hiddenDistrictInput.value = districtId;

    console.log('Hidden inputs updated:', {
        province: hiddenProvinceInput?.value,
        city: hiddenCityInput?.value,
        district: hiddenDistrictInput?.value
    });

    // Update shipping address if provided
    const addressSetting = document.querySelector('input[name="settings[shipping_origin_address]"]');
    if (addressSetting) {
        addressSetting.value = address;
        console.log('Address setting updated:', address);
    }

    // Auto-update RajaOngkir ID if available
    const citySelect = document.getElementById('modal_city');
    const selectedOption = citySelect.options[citySelect.selectedIndex];
    const rajaongkirId = selectedOption.dataset.rajaongkirId;

    console.log('RajaOngkir ID from city:', rajaongkirId);

    if (rajaongkirId) {
        const rajaongkirSetting = document.getElementById('rajaongkir_origin_city_id');
        if (rajaongkirSetting) {
            rajaongkirSetting.value = rajaongkirId;
            console.log('RajaOngkir setting updated:', rajaongkirId);
        }
    }

    // Update the current location display immediately (without reload)
    updateLocationDisplay(provinceName, cityName, districtName, address, rajaongkirId);

    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('locationPickerModal'));
    modal.hide();

    // Show success message
    Notiflix.Notify.success('📍 Shipping origin location updated! Remember to save your settings to persist changes.');
}

function updateLocationDisplay(provinceName, cityName, districtName, address) {
    // Update the current shipping origin display
    const locationDisplay = document.querySelector('.alert-info');
    if (locationDisplay) {
        const locationText = `${districtName}, ${cityName}, ${provinceName}`;
        const addressText = address ? `<div class="mb-1"><strong>Address:</strong> ${address}</div>` : '';

        locationDisplay.innerHTML = `
            <h6 class="alert-heading mb-2"><i class="fas fa-map-marker-alt me-2"></i>Current Shipping Origin</h6>
            <div class="mb-1"><strong>Location:</strong> ${locationText}</div>
            ${addressText}
        `;
    }
}

// API Sync Functions
function syncRajaOngkir() {
    Notiflix.Loading.circle('Syncing RajaOngkir data...');

    fetch('{{ route("admin.settings.sync") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ sync_rajaongkir: true })
    })
    .then(response => {
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        }
        // If not JSON, it's likely an error page - get the text
        return response.text().then(text => {
            throw new Error('Server returned HTML instead of JSON. Check server logs.');
        });
    })
    .then(data => {
        Notiflix.Loading.remove();
        showConnectionStatus('rajaongkir-status', data.rajaongkir);

        if (data.rajaongkir.status === 'success') {
            Notiflix.Notify.success('🌐 RajaOngkir API connection successful!');
        } else if (data.rajaongkir.status === 'warning') {
            Notiflix.Notify.warning('⚠️ RajaOngkir API: ' + data.rajaongkir.message);
        } else {
            Notiflix.Notify.failure('❌ RajaOngkir API connection failed: ' + data.rajaongkir.message);
        }
    })
    .catch(error => {
        Notiflix.Loading.remove();
        showConnectionStatus('rajaongkir-status', {
            status: 'error',
            message: 'Failed to test connection: ' + error.message
        });
        Notiflix.Notify.failure('❌ Failed to test RajaOngkir connection: ' + error.message);
    });
}

function syncMidtrans() {
    Notiflix.Loading.circle('Validating Midtrans settings...');

    fetch('{{ route("admin.settings.sync") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ sync_midtrans: true })
    })
    .then(response => {
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        }
        // If not JSON, it's likely an error page - get the text
        return response.text().then(text => {
            throw new Error('Server returned HTML instead of JSON. Check server logs.');
        });
    })
    .then(data => {
        Notiflix.Loading.remove();
        showConnectionStatus('midtrans-status', data.midtrans);

        if (data.midtrans.status === 'success') {
            Notiflix.Notify.success('💳 Midtrans settings validated successfully!');
        } else {
            Notiflix.Notify.failure('❌ Midtrans validation failed: ' + data.midtrans.message);
        }
    })
    .catch(error => {
        Notiflix.Loading.remove();
        showConnectionStatus('midtrans-status', {
            status: 'error',
            message: 'Failed to test connection: ' + error.message
        });
        Notiflix.Notify.failure('❌ Failed to test Midtrans connection: ' + error.message);
    });
}

function syncAllAPIs() {
    Notiflix.Loading.circle('Syncing all APIs...');

    fetch('{{ route("admin.settings.sync") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            sync_rajaongkir: true,
            sync_midtrans: true
        })
    })
    .then(response => {
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        }
        // If not JSON, it's likely an error page - get the text
        return response.text().then(text => {
            throw new Error('Server returned HTML instead of JSON. Check server logs.');
        });
    })
    .then(data => {
        Notiflix.Loading.remove();

        let successCount = 0;
        let warningCount = 0;
        let totalTests = 0;

        if (data.rajaongkir) {
            showConnectionStatus('rajaongkir-status', data.rajaongkir);
            totalTests++;
            if (data.rajaongkir.status === 'success') successCount++;
            if (data.rajaongkir.status === 'warning') warningCount++;
        }
        if (data.midtrans) {
            showConnectionStatus('midtrans-status', data.midtrans);
            totalTests++;
            if (data.midtrans.status === 'success') successCount++;
            if (data.midtrans.status === 'warning') warningCount++;
        }

        // Summary notification
        if (successCount === totalTests) {
            Notiflix.Notify.success(`🎉 All API connections successful! (${successCount}/${totalTests})`);
        } else if ((successCount + warningCount) === totalTests) {
            Notiflix.Notify.warning(`⚠️ All APIs responded (some with warnings): ${successCount} success, ${warningCount} warnings`);
        } else if (successCount > 0) {
            Notiflix.Notify.warning(`⚠️ Partial success: ${successCount}/${totalTests} connections working`);
        } else {
            Notiflix.Notify.failure(`❌ All API connections failed (${successCount}/${totalTests})`);
        }
    })
    .catch(error => {
        Notiflix.Loading.remove();
        Notiflix.Notify.failure('❌ Failed to test connections: ' + error.message);
    });
}

function showConnectionStatus(elementId, result) {
    const element = document.getElementById(elementId);
    let alertClass, icon, statusText;

    switch (result.status) {
        case 'success':
            alertClass = 'alert-success';
            icon = 'fa-check-circle';
            statusText = 'Success!';
            break;
        case 'warning':
            alertClass = 'alert-warning';
            icon = 'fa-exclamation-triangle';
            statusText = 'Warning!';
            break;
        default:
            alertClass = 'alert-danger';
            icon = 'fa-exclamation-triangle';
            statusText = 'Error!';
            break;
    }

    element.innerHTML = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="fas ${icon} me-2"></i>
            <strong>${statusText}</strong> ${result.message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    element.style.display = 'block';

    // Auto hide after 15 seconds for warnings/errors, 10 for success
    const hideTimeout = result.status === 'success' ? 10000 : 15000;
    setTimeout(() => {
        element.style.display = 'none';
    }, hideTimeout);
}

// Reset Changes Confirmation
function confirmResetChanges() {
    Notiflix.Confirm.show(
        'Reset Changes',
        'Are you sure you want to reset all changes? This will reload the page and discard any unsaved modifications.',
        'Yes, Reset',
        'Cancel',
        function okCb() {
            Notiflix.Loading.circle('Resetting changes...');
            // Add a small delay to show the loading indicator
            setTimeout(() => {
                location.reload();
            }, 500);
        },
        function cancelCb() {
            // User cancelled, do nothing
        },
        {
            width: '350px',
            borderRadius: '8px',
            titleColor: '#dc3545',
            okButtonBackground: '#dc3545',
            cssAnimationStyle: 'zoom'
        }
    );
}

// Legacy loading modal functions removed - using Notiflix instead

// Form validation and submission
document.getElementById('settingsForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent default form submission

    // Show loading
    Notiflix.Loading.circle('Saving system settings...');

    // Get form data
    const formData = new FormData(this);

    // Submit via fetch for better control
    fetch(this.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.text();
    })
    .then(html => {
        Notiflix.Loading.remove();

        // Check if response contains success or error
        if (html.includes('System settings updated successfully')) {
            Notiflix.Notify.success('✅ System settings saved successfully!');

            // Optionally reload page to show updated values
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else if (html.includes('error')) {
            // Try to extract error message from HTML
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const errorDiv = doc.querySelector('.alert-danger');
            const errorMessage = errorDiv ? errorDiv.textContent.trim() : 'An error occurred while saving settings';

            Notiflix.Notify.failure('❌ ' + errorMessage);
        } else {
            // Fallback - redirect normally
            document.body.innerHTML = html;
            Notiflix.Notify.success('✅ System settings saved successfully!');
        }
    })
    .catch(error => {
        Notiflix.Loading.remove();
        console.error('Save failed:', error);
        Notiflix.Notify.failure('❌ Failed to save settings: ' + error.message);
    });
});

// Show session messages with Notiflix on page load
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        Notiflix.Notify.success('{{ session('success') }}');
    @endif

    @if(session('error'))
        Notiflix.Notify.failure('{{ session('error') }}');
    @endif

    // Auto-hide bootstrap alerts after 3 seconds (backup)
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(function(alert) {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            bsAlert.close();
        });
    }, 3000);
});
</script>
@endpush