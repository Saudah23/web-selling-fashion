<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class SystemSettingsController extends Controller
{
    /**
     * Display system settings page
     */
    public function index()
    {
        $settings = SystemSetting::all()->groupBy('group');
        $shippingOrigin = SystemSetting::getShippingOriginDetails();

        return view('admin.settings.index', compact('settings', 'shippingOrigin'));
    }

    /**
     * Update system settings
     */
    public function update(Request $request)
    {
        // Basic validation - allow empty values for optional fields
        $validator = Validator::make($request->all(), [
            'settings' => 'required|array',
            'settings.*' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Validation failed');
        }

        try {
            foreach ($request->settings as $key => $value) {
                $setting = SystemSetting::where('key', $key)->first();

                if ($setting) {
                    // Skip validation for empty optional fields
                    if ($this->isOptionalField($key) && empty($value)) {
                        // Still save empty value to clear the setting
                        SystemSetting::set($key, '', $setting->type);
                        continue;
                    }

                    // Validate specific setting types only for non-empty values
                    if (!empty($value)) {
                        $this->validateSettingValue($key, $value, $setting->type);
                    }

                    SystemSetting::set($key, $value, $setting->type);
                }
            }


            return redirect()->back()->with('success', 'System settings updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update settings: ' . $e->getMessage());
        }
    }

    /**
     * Define which fields are optional and can be empty
     */
    private function isOptionalField(string $key): bool
    {
        $optionalFields = [
            // RajaOngkir fields (optional until API key is configured)
            'rajaongkir_api_key',
            'rajaongkir_origin_city_id',

            // Midtrans fields (optional until configured)
            'midtrans_server_key',
            'midtrans_client_key',
            'midtrans_merchant_id',

            // Other optional fields
            'shipping_origin_address',
        ];

        return in_array($key, $optionalFields);
    }

    /**
     * Get public settings for frontend API
     */
    public function publicSettings()
    {
        return response()->json([
            'settings' => SystemSetting::getPublic()
        ]);
    }

    /**
     * Sync API data and validate connections (RajaOngkir, Midtrans)
     */
    public function syncAPIs(Request $request)
    {
        $results = [];

        // Sync RajaOngkir API
        if ($request->has('sync_rajaongkir')) {
            $results['rajaongkir'] = $this->syncRajaOngkirData();
        }

        // Validate Midtrans API
        if ($request->has('sync_midtrans')) {
            $results['midtrans'] = $this->validateMidtransConnection();
        }

        return response()->json($results);
    }

    /**
     * Validate setting value based on type
     */
    private function validateSettingValue(string $key, $value, string $type): void
    {
        // Skip validation for empty values (handled by isOptionalField)
        if (empty($value) && $this->isOptionalField($key)) {
            return;
        }

        switch ($type) {
            case 'json':
                json_decode($value, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \InvalidArgumentException("Invalid JSON for setting: {$key}");
                }
                break;

            case 'number':
            case 'integer':
                if (!is_numeric($value)) {
                    throw new \InvalidArgumentException("Setting {$key} must be numeric");
                }
                break;

            case 'boolean':
                if (!in_array($value, ['0', '1', 'true', 'false'])) {
                    throw new \InvalidArgumentException("Setting {$key} must be boolean");
                }
                break;
        }

        // Specific validations - only for non-empty values
        switch ($key) {
            case 'contact_email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    throw new \InvalidArgumentException("Invalid email format");
                }
                break;

            case 'tax_rate':
                if (!empty($value) && ($value < 0 || $value > 100)) {
                    throw new \InvalidArgumentException("Tax rate must be between 0-100%");
                }
                break;

            case 'midtrans_environment':
                if (!empty($value) && !in_array($value, ['sandbox', 'production'])) {
                    throw new \InvalidArgumentException("Midtrans environment must be 'sandbox' or 'production'");
                }
                break;

            // Required fields that cannot be empty
            case 'app_name':
            case 'currency_code':
                if (empty($value)) {
                    throw new \InvalidArgumentException("Setting {$key} is required and cannot be empty");
                }
                break;
        }
    }

    /**
     * Sync RajaOngkir data and update shipping origin city ID
     */
    private function syncRajaOngkirData(): array
    {
        try {
            $apiKey = SystemSetting::get('rajaongkir_api_key');

            if (!$apiKey) {
                return ['status' => 'error', 'message' => 'RajaOngkir API key not configured'];
            }

            // Get current shipping origin
            $provinceId = SystemSetting::get('shipping_origin_province_id');
            $cityId = SystemSetting::get('shipping_origin_city_id');
            $districtId = SystemSetting::get('shipping_origin_district_id');

            if (!$provinceId || !$cityId || !$districtId) {
                return ['status' => 'error', 'message' => 'Shipping origin location not configured. Please set your shipping location first.'];
            }

            // Test API connection first
            $response = Http::withHeaders([
                'Key' => $apiKey,
                'accept' => 'application/json'
            ])->get('https://rajaongkir.komerce.id/api/v1/destination/province');

            $data = $response->json();

            // Handle API errors
            if (isset($data['meta']['status']) && $data['meta']['status'] === 'error') {
                $message = $data['meta']['message'] ?? 'API returned error status';
                $code = $data['meta']['code'] ?? $response->status();

                if ($code == 429) {
                    return ['status' => 'warning', 'message' => 'Daily API limit exceeded. Cannot sync data now. Please try tomorrow or upgrade your RajaOngkir plan.'];
                }

                return ['status' => 'error', 'message' => "RajaOngkir API Error: {$message} (Code: {$code})"];
            }

            if (!$response->successful() || !isset($data['data'])) {
                return ['status' => 'error', 'message' => 'Failed to connect to RajaOngkir API'];
            }

            // Find the city in our database and sync RajaOngkir ID
            $city = \App\Models\City::where('id', $cityId)
                ->where('province_id', $provinceId)
                ->first();

            if (!$city) {
                return ['status' => 'error', 'message' => 'Selected shipping city not found in database'];
            }

            // If city already has RajaOngkir ID, update the setting
            if ($city->rajaongkir_id) {
                SystemSetting::set('rajaongkir_origin_city_id', $city->rajaongkir_id, 'number');
                return ['status' => 'success', 'message' => "RajaOngkir sync successful! Origin city ID updated to {$city->rajaongkir_id} for {$city->name}"];
            }

            // City doesn't have RajaOngkir ID, need full sync
            return ['status' => 'warning', 'message' => "RajaOngkir API connected but {$city->name} doesn't have RajaOngkir ID. Run 'composer sync-rajaongkir' to sync all cities data."];

        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Sync failed: ' . $e->getMessage()];
        }
    }

    /**
     * Validate Midtrans API connection and settings
     */
    private function validateMidtransConnection(): array
    {
        try {
            $serverKey = SystemSetting::get('midtrans_server_key');
            $clientKey = SystemSetting::get('midtrans_client_key');
            $merchantId = SystemSetting::get('midtrans_merchant_id');
            $environment = SystemSetting::get('midtrans_environment', 'sandbox');

            // Check required fields
            $missingFields = [];
            if (!$serverKey) $missingFields[] = 'Server Key';
            if (!$clientKey) $missingFields[] = 'Client Key';
            if (!$merchantId) $missingFields[] = 'Merchant ID';

            if (!empty($missingFields)) {
                return ['status' => 'error', 'message' => 'Missing required fields: ' . implode(', ', $missingFields)];
            }

            $baseUrl = $environment === 'production'
                ? 'https://api.midtrans.com'
                : 'https://api.sandbox.midtrans.com';

            // Validate API connection with a test call
            $response = Http::withBasicAuth($serverKey, '')
                ->get("{$baseUrl}/v2/test-transaction-id/status");

            if ($response->successful()) {
                $data = $response->json();
                // Check if we get expected 404 response for non-existent transaction
                if (isset($data['status_code']) && $data['status_code'] === '404') {
                    return [
                        'status' => 'success',
                        'message' => "Midtrans {$environment} API validated successfully! All credentials are working."
                    ];
                }
                // If we get any other successful response, it's also valid connection
                if (isset($data['status_code'])) {
                    return [
                        'status' => 'success',
                        'message' => "Midtrans {$environment} API validated successfully!"
                    ];
                }
            }

            return ['status' => 'error', 'message' => 'Invalid Midtrans API credentials or network error'];

        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Validation failed: ' . $e->getMessage()];
        }
    }
}