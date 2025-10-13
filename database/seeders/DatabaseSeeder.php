<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SystemSettingsSeeder::class,
            // Auto-import wilayah data if not exists
            StreamingWilayahSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            BannerSeeder::class,
        ]);

        // Auto-sync RajaOngkir data if API key is available
        $this->syncRajaOngkirIfConfigured();
    }

    /**
     * Automatically sync RajaOngkir data if API key is configured
     */
    private function syncRajaOngkirIfConfigured(): void
    {
        $this->command->info('🔄 Checking for RajaOngkir auto-sync...');

        // Get API key from system settings or environment
        $apiKey = env('RAJAONGKIR_API_KEY');

        if (!$apiKey) {
            // Try to get from seeded system settings
            try {
                $apiKey = \App\Models\SystemSetting::get('rajaongkir_api_key');
            } catch (\Exception $e) {
                // Settings table might not be ready yet
            }
        }

        if ($apiKey) {
            $this->command->info('🌐 API key found, syncing RajaOngkir data...');

            try {
                \Illuminate\Support\Facades\Artisan::call('rajaongkir:sync', [
                    '--api-key' => $apiKey
                ]);

                $this->command->info('✅ RajaOngkir data synchronized successfully!');

                // Display output from the command
                $output = \Illuminate\Support\Facades\Artisan::output();
                if ($output) {
                    $this->command->line($output);
                }

            } catch (\Exception $e) {
                $this->command->warn('⚠️ RajaOngkir sync failed: ' . $e->getMessage());
                $this->command->line('You can manually sync later with: php artisan rajaongkir:sync --api-key=YOUR_KEY');
            }
        } else {
            $this->command->info('ℹ️ No RajaOngkir API key found, skipping auto-sync');
            $this->command->line('Add RAJAONGKIR_API_KEY to .env or system settings for auto-sync');
        }
    }
}
