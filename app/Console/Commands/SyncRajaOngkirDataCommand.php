<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Province;
use App\Models\City;
use App\Models\SystemSetting;

class SyncRajaOngkirDataCommand extends Command
{
    protected $signature = 'rajaongkir:sync {--api-key=} {--test-mode}';
    protected $description = 'Sync RajaOngkir province and city data with our database';

    public function handle()
    {
        $this->info('🌐 RajaOngkir Data Synchronization');
        $this->info('=====================================');

        $apiKey = $this->option('api-key') ?: SystemSetting::get('rajaongkir_api_key');

        if (!$apiKey) {
            $this->error('❌ RajaOngkir API key not found!');
            $this->line('Please provide API key via:');
            $this->line('  --api-key=YOUR_API_KEY');
            $this->line('  Or set it in system settings');
            return 1;
        }

        $testMode = $this->option('test-mode');
        if ($testMode) {
            $this->warn('⚠️ Running in TEST MODE - no database changes');
        }

        try {
            // Step 1: Sync Provinces
            $this->info('📍 Step 1: Syncing provinces...');
            $this->syncProvinces($apiKey, $testMode);

            // Step 2: Sync Cities
            $this->info('🏙️ Step 2: Syncing cities...');
            $this->syncCities($apiKey, $testMode);

            // Step 3: Report Summary
            $this->showSummary();

            $this->info('✅ RajaOngkir synchronization completed!');
            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Synchronization failed: ' . $e->getMessage());
            return 1;
        }
    }

    private function syncProvinces(string $apiKey, bool $testMode): void
    {
        $response = Http::withHeaders([
                'Key' => $apiKey,
                'accept' => 'application/json'
            ])
            ->get('https://rajaongkir.komerce.id/api/v1/destination/province');

        $data = $response->json();

        // Check for API errors
        if (isset($data['meta']['status']) && $data['meta']['status'] === 'error') {
            $message = $data['meta']['message'] ?? 'Unknown API error';
            $code = $data['meta']['code'] ?? $response->status();

            if ($code == 429) {
                throw new \Exception("RajaOngkir daily API limit exceeded. Please try again tomorrow or upgrade your API plan. ({$message})");
            }

            throw new \Exception("RajaOngkir API error: {$message} (Code: {$code})");
        }

        if (!$response->successful() || !isset($data['data'])) {
            throw new \Exception('Failed to fetch provinces from RajaOngkir API');
        }

        $rajaProvinces = $data['data'];
        $matched = 0;
        $unmatched = [];

        foreach ($rajaProvinces as $rajaProv) {
            $rajaId = $rajaProv['id'];
            $rajaName = $rajaProv['name'];

            // Try exact match first
            $province = Province::where('name', $rajaName)->first();

            // Try fuzzy matching
            if (!$province) {
                $province = $this->findProvinceByFuzzyMatch($rajaName);
            }

            if ($province) {
                if (!$testMode) {
                    $province->update(['rajaongkir_id' => $rajaId]);
                }
                $this->line("✅ {$province->name} → RajaOngkir ID: {$rajaId}");
                $matched++;
            } else {
                $unmatched[] = $rajaName;
                $this->warn("⚠️ No match found for: {$rajaName}");
            }
        }

        $this->info("📊 Provinces: {$matched} matched, " . count($unmatched) . " unmatched");
    }

    private function syncCities(string $apiKey, bool $testMode): void
    {
        // Get all provinces with RajaOngkir IDs first
        $provinces = Province::whereNotNull('rajaongkir_id')->get();

        $matched = 0;
        $unmatched = [];

        foreach ($provinces as $province) {

            $response = Http::withHeaders([
                    'Key' => $apiKey,
                    'accept' => 'application/json'
                ])
                ->get("https://rajaongkir.komerce.id/api/v1/destination/city/{$province->rajaongkir_id}");

            if (!$response->successful()) {
                $this->warn("⚠️ Failed to fetch cities for province: {$province->name}");
                continue;
            }

            $data = $response->json();

            if (!isset($data['data'])) {
                $this->warn("⚠️ Invalid response for province: {$province->name}");
                continue;
            }

            $rajaCities = $data['data'];

            foreach ($rajaCities as $rajaCity) {
                $rajaId = $rajaCity['id'];
                $rajaName = $rajaCity['name'];

                // Try to find city in our database
                $city = $this->findCityInProvince($rajaName, $province->id);

                if ($city) {
                    if (!$testMode) {
                        $city->update([
                            'rajaongkir_id' => $rajaId
                        ]);
                    }
                    $this->line("✅ {$city->name} → RajaOngkir ID: {$rajaId}");
                    $matched++;
                } else {
                    $unmatched[] = "{$rajaName} in {$province->name}";
                    $this->warn("⚠️ No match found for: {$rajaName}");
                }
            }
        }

        $this->info("📊 Cities: {$matched} matched, " . count($unmatched) . " unmatched");
    }

    private function findProvinceByFuzzyMatch(string $rajaName): ?Province
    {
        // Common name variations - mapping RajaOngkir names to our database names
        $variations = [
            'DKI JAKARTA' => ['Jakarta', 'DKI Jakarta', 'Daerah Khusus Ibukota Jakarta'],
            'DI YOGYAKARTA' => ['Yogyakarta', 'DIY', 'DI Yogyakarta', 'Daerah Istimewa Yogyakarta'],
            'NANGGROE ACEH DARUSSALAM (NAD)' => ['Aceh', 'Nanggroe Aceh Darussalam'],
            'NUSA TENGGARA BARAT (NTB)' => ['Nusa Tenggara Barat'],
            'NUSA TENGGARA TIMUR (NTT)' => ['Nusa Tenggara Timur'],
        ];

        // Direct variations check
        foreach ($variations as $rajaKey => $ourNames) {
            if ($rajaName === $rajaKey) {
                foreach ($ourNames as $ourName) {
                    $province = Province::where('name', 'LIKE', "%{$ourName}%")->first();
                    if ($province) return $province;
                }
            }
        }

        // Clean name for partial matching
        $cleanName = str_replace(['(NTB)', '(NTT)', '(NAD)', 'DKI ', 'DI '], '', $rajaName);
        $cleanName = trim($cleanName);

        // Partial match
        return Province::where('name', 'LIKE', "%{$cleanName}%")->first();
    }

    private function findCityInProvince(string $rajaName, int $provinceId): ?City
    {
        // Try exact match first
        $city = City::where('province_id', $provinceId)
            ->where('name', $rajaName)
            ->first();

        if ($city) return $city;

        // Try with common prefixes
        $prefixes = ['Kota ', 'Kabupaten ', 'Kab. ', 'Kep. '];
        foreach ($prefixes as $prefix) {
            $withPrefix = $prefix . $rajaName;
            $city = City::where('province_id', $provinceId)
                ->where('name', $withPrefix)
                ->first();
            if ($city) return $city;
        }

        // Try partial match
        return City::where('province_id', $provinceId)
            ->where('name', 'LIKE', "%{$rajaName}%")
            ->first();
    }

    private function showSummary(): void
    {
        $provincesWithRaja = Province::whereNotNull('rajaongkir_id')->count();
        $citiesWithRaja = City::whereNotNull('rajaongkir_id')->count();
        $totalProvinces = Province::count();
        $totalCities = City::count();

        $this->info('📈 Synchronization Summary:');
        $this->table(
            ['Type', 'With RajaOngkir ID', 'Total', 'Coverage'],
            [
                ['Provinces', $provincesWithRaja, $totalProvinces, round(($provincesWithRaja / $totalProvinces) * 100, 1) . '%'],
                ['Cities', $citiesWithRaja, $totalCities, round(($citiesWithRaja / $totalCities) * 100, 1) . '%'],
            ]
        );
    }
}