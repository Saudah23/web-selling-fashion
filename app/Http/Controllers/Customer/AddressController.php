<?php

namespace App\Http\Controllers\Customer;

use App\Models\CustomerAddress;
use App\Models\Province;
use App\Models\City;
use App\Models\District;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers\Controller;

class AddressController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $addresses = CustomerAddress::with(['province', 'city', 'district', 'village'])
            ->forUser(Auth::id())
            ->orderByDesc('is_default')
            ->orderByDesc('created_at')
            ->get();

        $provinces = Province::orderBy('name')->get();

        return view('profile.addresses', compact('addresses', 'provinces'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:50',
            'recipient_name' => 'required|string|max:100',
            'recipient_phone' => 'required|string|max:20',
            'province_id' => 'required|exists:provinces,id',
            'city_id' => 'required|exists:cities,id',
            'district_id' => 'required|exists:districts,id',
            'village_id' => 'required|exists:villages,id',
            'address_detail' => 'required|string|max:500',
            'postal_code' => 'nullable|string|max:10',
            'notes' => 'nullable|string|max:200',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_default' => 'boolean'
        ]);

        $validated['user_id'] = Auth::id();

        // Validate location hierarchy
        $this->validateLocationHierarchy($validated);

        $address = CustomerAddress::create($validated);

        if ($validated['is_default'] ?? false) {
            $address->makeDefault();
        }

        return response()->json([
            'success' => true,
            'message' => 'Address added successfully',
            'address' => $address->load(['province', 'city', 'district', 'village'])
        ]);
    }

    public function show(CustomerAddress $address)
    {
        $this->authorize('view', $address);

        return response()->json([
            'address' => $address->load(['province', 'city', 'district', 'village'])
        ]);
    }

    public function edit(CustomerAddress $address)
    {
        $this->authorize('update', $address);

        $provinces = Province::orderBy('name')->get();
        $cities = City::where('province_id', $address->province_id)->orderBy('name')->get();
        $districts = District::where('city_id', $address->city_id)->orderBy('name')->get();
        $villages = Village::where('district_id', $address->district_id)->orderBy('name')->get();

        return response()->json([
            'address' => $address,
            'provinces' => $provinces,
            'cities' => $cities,
            'districts' => $districts,
            'villages' => $villages
        ]);
    }

    public function update(Request $request, CustomerAddress $address)
    {
        $this->authorize('update', $address);

        $validated = $request->validate([
            'label' => 'required|string|max:50',
            'recipient_name' => 'required|string|max:100',
            'recipient_phone' => 'required|string|max:20',
            'province_id' => 'required|exists:provinces,id',
            'city_id' => 'required|exists:cities,id',
            'district_id' => 'required|exists:districts,id',
            'village_id' => 'required|exists:villages,id',
            'address_detail' => 'required|string|max:500',
            'postal_code' => 'nullable|string|max:10',
            'notes' => 'nullable|string|max:200',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_default' => 'boolean'
        ]);

        // Validate location hierarchy
        $this->validateLocationHierarchy($validated);

        $address->update($validated);

        if ($validated['is_default'] ?? false) {
            $address->makeDefault();
        }

        return response()->json([
            'success' => true,
            'message' => 'Address updated successfully',
            'address' => $address->load(['province', 'city', 'district', 'village'])
        ]);
    }

    public function destroy(CustomerAddress $address)
    {
        $this->authorize('delete', $address);

        $wasDefault = $address->is_default;
        $userId = $address->user_id;

        $address->delete();

        // If deleted address was default, make another one default
        if ($wasDefault) {
            $newDefault = CustomerAddress::forUser($userId)->first();
            if ($newDefault) {
                $newDefault->makeDefault();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully'
        ]);
    }

    public function makeDefault(CustomerAddress $address)
    {
        $this->authorize('update', $address);

        $address->makeDefault();

        return response()->json([
            'success' => true,
            'message' => 'Default address updated successfully'
        ]);
    }

    // API endpoints for location cascade
    public function getProvinces()
    {
        $provinces = Province::orderBy('name')
            ->get(['id', 'name', 'rajaongkir_id']);

        return response()->json($provinces);
    }

    public function getCities(Request $request)
    {
        $cities = City::where('province_id', $request->province_id)
            ->orderBy('name')
            ->get(['id', 'name', 'rajaongkir_id', 'rajaongkir_type']);

        return response()->json($cities);
    }

    public function getDistricts(Request $request)
    {
        $districts = District::where('city_id', $request->city_id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($districts);
    }

    public function getVillages(Request $request)
    {
        $villages = Village::where('district_id', $request->district_id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($villages);
    }

    private function validateLocationHierarchy(array $data)
    {
        // Validate city belongs to province
        $city = City::where('id', $data['city_id'])
            ->where('province_id', $data['province_id'])
            ->first();

        if (!$city) {
            throw ValidationException::withMessages([
                'city_id' => 'Selected city does not belong to the selected province.'
            ]);
        }

        // Validate district belongs to city
        $district = District::where('id', $data['district_id'])
            ->where('city_id', $data['city_id'])
            ->first();

        if (!$district) {
            throw ValidationException::withMessages([
                'district_id' => 'Selected district does not belong to the selected city.'
            ]);
        }

        // Validate village belongs to district
        $village = Village::where('id', $data['village_id'])
            ->where('district_id', $data['district_id'])
            ->first();

        if (!$village) {
            throw ValidationException::withMessages([
                'village_id' => 'Selected village does not belong to the selected district.'
            ]);
        }
    }
}