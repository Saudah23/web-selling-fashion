<?php

namespace App\Services;

use App\Models\SystemSetting;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RajaOngkirService
{
    private string $apiKey;
    private string $baseUrl = 'https://rajaongkir.komerce.id/api/v1';

    public function __construct()
    {
        $this->apiKey = SystemSetting::get('rajaongkir_api_key')
            ?? config('services.rajaongkir.api_key')
            ?? env('RAJAONGKIR_API_KEY');

        if (!$this->apiKey) {
            throw new Exception('RajaOngkir API key not configured');
        }
    }

    /**
     * Get shipping cost calculation
     */
    public function getShippingCost(int $originCityId, int $destinationCityId, int $weight, array $couriers = ['jne', 'jnt']): array
    {
        // Validate inputs
        if ($originCityId <= 0 || $destinationCityId <= 0) {
            throw new Exception('Invalid origin or destination city ID');
        }

        if ($weight <= 0) {
            throw new Exception('Invalid weight. Weight must be greater than 0');
        }

        if (empty($couriers)) {
            $couriers = ['jne', 'jnt']; // Default couriers
        }

        $allResults = [];
        $failedCouriers = [];

        // RajaOngkir API V2 requires one courier per request
        foreach ($couriers as $courier) {
            try {
                $response = Http::asForm()->withHeaders([
                    'key' => $this->apiKey,
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ])->post($this->baseUrl . '/calculate/domestic-cost', [
                    'origin' => $originCityId,
                    'destination' => $destinationCityId,
                    'weight' => $weight,
                    'courier' => $courier
                ]);

                if (!$response->successful()) {
                    $failedCouriers[] = $courier;
                    Log::warning('RajaOngkir API request failed for courier: ' . $courier, [
                        'status' => $response->status(),
                        'response' => $response->body(),
                        'origin' => $originCityId,
                        'destination' => $destinationCityId
                    ]);
                    continue; // Skip this courier and try the next one
                }

                $data = $response->json();

                if (!isset($data['meta']) || $data['meta']['status'] !== 'success') {
                    Log::warning('RajaOngkir API returned error for courier: ' . $courier, [
                        'response' => $data
                    ]);
                    continue;
                }

                if (isset($data['data']) && is_array($data['data'])) {
                    $allResults = array_merge($allResults, $data['data']);
                }

            } catch (Exception $e) {
                $failedCouriers[] = $courier;
                Log::error('RajaOngkir API exception for courier: ' . $courier, [
                    'error' => $e->getMessage(),
                    'origin' => $originCityId,
                    'destination' => $destinationCityId,
                    'weight' => $weight
                ]);
                continue; // Continue with next courier
            }
        }

        if (empty($allResults)) {
            $errorMessage = 'No shipping options available from any courier';
            if (!empty($failedCouriers)) {
                $errorMessage .= '. Failed couriers: ' . implode(', ', $failedCouriers);
            }
            throw new Exception($errorMessage);
        }

        Log::info('RajaOngkir shipping calculation successful', [
            'origin' => $originCityId,
            'destination' => $destinationCityId,
            'weight' => $weight,
            'total_options' => count($allResults),
            'failed_couriers' => $failedCouriers
        ]);

        return $this->formatShippingOptions($allResults);
    }

    /**
     * Format shipping options for frontend
     */
    private function formatShippingOptions(array $results): array
    {
        $options = [];

        foreach ($results as $service) {
            // RajaOngkir V2 response format: direct array of services
            if (!isset($service['cost']) || !isset($service['name'])) {
                continue;
            }

            $options[] = [
                'courier_code' => $service['code'] ?? 'unknown',
                'courier_name' => $service['name'],
                'service_code' => $service['service'] ?? 'regular',
                'service_name' => $service['description'] ?? $service['service'] ?? 'Regular Service',
                'cost' => (int) $service['cost'],
                'formatted_cost' => 'Rp ' . number_format($service['cost'], 0, ',', '.'),
                'etd' => $service['etd'] ?? 'Unknown',
                'formatted_etd' => $this->formatEtd($service['etd'] ?? ''),
                'full_service_name' => $service['name'] . ' - ' . ($service['description'] ?? $service['service'] ?? 'Regular')
            ];
        }

        // Sort by cost ascending
        usort($options, fn($a, $b) => $a['cost'] <=> $b['cost']);

        return $options;
    }

    /**
     * Format ETD (Estimated Time Delivery)
     */
    private function formatEtd(string $etd): string
    {
        if (empty($etd)) {
            return 'Unknown';
        }

        // Handle format like "2-3" or "1-2 HARI"
        $etd = strtoupper(trim($etd));

        if (str_contains($etd, 'HARI')) {
            return $etd;
        }

        return $etd . ' Hari';
    }

    /**
     * Get origin city ID from system settings
     */
    public function getOriginCityId(): ?int
    {
        return SystemSetting::get('rajaongkir_origin_city_id');
    }

    /**
     * Get supported couriers from system settings
     */
    public function getSupportedCouriers(): array
    {
        $couriers = SystemSetting::get('rajaongkir_couriers');

        if (is_string($couriers)) {
            $couriers = json_decode($couriers, true);
        }

        return is_array($couriers) ? $couriers : ['jne', 'jnt'];
    }

    /**
     * Calculate total weight from cart items
     */
    public function calculateTotalWeight($cartItems): int
    {
        $totalWeight = 0;

        foreach ($cartItems as $item) {
            $productWeight = $item->product->weight ?? 500; // Default 500g if not set
            $totalWeight += $productWeight * $item->quantity;
        }

        // Minimum weight 1000g (1kg) for shipping calculation
        return max($totalWeight, 1000);
    }

    /**
     * Validate shipping address has RajaOngkir city ID
     */
    public function validateShippingAddress(array $address): bool
    {
        return isset($address['city_id']) &&
               isset($address['rajaongkir_city_id']) &&
               !empty($address['rajaongkir_city_id']);
    }

    /**
     * Test API connection
     */
    public function testConnection(): array
    {
        try {
            $response = Http::withHeaders([
                'Key' => $this->apiKey,
                'accept' => 'application/json'
            ])->get($this->baseUrl . '/destination/province');

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'message' => 'RajaOngkir API connection successful',
                    'provinces_count' => count($data['data'] ?? [])
                ];
            }

            return [
                'success' => false,
                'message' => 'RajaOngkir API connection failed: ' . $response->status(),
                'status_code' => $response->status()
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'RajaOngkir API connection error: ' . $e->getMessage()
            ];
        }
    }
}