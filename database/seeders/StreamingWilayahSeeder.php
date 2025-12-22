<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class StreamingWilayahSeeder extends Seeder
{
    private $provinceMap = [];
    private $cityMap = [];
    private $districtMap = [];

    public function run(): void
    {
        // Check if data already exists
        $provinceCount = DB::table('provinces')->count();
        if ($provinceCount > 0) {
            $this->command->info('📍 Wilayah data already exists (' . $provinceCount . ' provinces)');
            $this->command->info('🔄 Use --fresh flag to reimport: php artisan wilayah:stream-import --fresh');
            return;
        }

        $sqlFile = database_path('wilayah.sql');

        if (!File::exists($sqlFile)) {
            $this->command->error('❌ wilayah.sql file not found!');
            $this->command->line('Please ensure database/wilayah.sql exists');
            return;
        }

        $this->command->info('🚀 Starting streaming wilayah import...');
        $startTime = microtime(true);

        try {
            $driver = DB::connection()->getDriverName();

            // Optimize database settings (MySQL only)
            if ($driver === 'mysql') {
                DB::unprepared("SET FOREIGN_KEY_CHECKS=0; SET AUTOCOMMIT=0;");
            }

            // Clear existing data
            $this->command->info('🗑️  Clearing existing data...');
            DB::table('villages')->delete();
            DB::table('districts')->delete();
            DB::table('cities')->delete();
            DB::table('provinces')->delete();

            // Reset auto increment (MySQL only, SQLite auto-handles this)
            if ($driver === 'mysql') {
                DB::unprepared("
                    ALTER TABLE provinces AUTO_INCREMENT = 1;
                    ALTER TABLE cities AUTO_INCREMENT = 1;
                    ALTER TABLE districts AUTO_INCREMENT = 1;
                    ALTER TABLE villages AUTO_INCREMENT = 1;
                ");
            }

            // Stream process each level separately
            $this->processProvinces($sqlFile);
            $this->processCities($sqlFile);
            $this->processDistricts($sqlFile);
            $this->processVillages($sqlFile);

            if ($driver === 'mysql') {
                DB::unprepared("COMMIT;");
            }

            $duration = round(microtime(true) - $startTime, 2);
            $this->command->newLine();
            $this->command->info("✅ Streaming import completed in {$duration} seconds!");

            $this->showResults();

        } catch (\Exception $e) {
            $driver = DB::connection()->getDriverName();
            if ($driver === 'mysql') {
                DB::unprepared("ROLLBACK;");
            }
            $this->command->error("❌ Import failed: " . $e->getMessage());
            throw $e;
        } finally {
            $driver = DB::connection()->getDriverName();
            if ($driver === 'mysql') {
                DB::unprepared("SET FOREIGN_KEY_CHECKS=1; SET AUTOCOMMIT=1;");
            }
        }
    }

    private function processProvinces(string $sqlFile): void
    {
        $this->command->info('🏛️  Processing provinces...');
        $provinces = [];
        $count = 0;

        $handle = fopen($sqlFile, 'r');
        while (($line = fgets($handle)) !== false) {
            if (preg_match("/\('(\d+)','([^']+)'\)/", $line, $matches)) {
                // Only single level codes (provinces)
                if (!str_contains($matches[1], '.')) {
                    $provinces[] = [
                        'wilayah_id' => $matches[1],
                        'name' => str_replace("''", "'", $matches[2]),
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                    $count++;

                    // Insert in batches of 10
                    if (count($provinces) >= 10) {
                        DB::table('provinces')->insert($provinces);
                        $provinces = [];
                    }
                }
            }
        }

        // Insert remaining provinces
        if (!empty($provinces)) {
            DB::table('provinces')->insert($provinces);
        }

        fclose($handle);

        // Build province lookup
        $this->provinceMap = DB::table('provinces')->pluck('id', 'wilayah_id')->toArray();
        $this->command->info("  ↳ Inserted {$count} provinces");
    }

    private function processCities(string $sqlFile): void
    {
        $this->command->info('🏙️  Processing cities...');
        $cities = [];
        $count = 0;

        $handle = fopen($sqlFile, 'r');
        while (($line = fgets($handle)) !== false) {
            if (preg_match("/\('(\d+\.\d+)','([^']+)'\)/", $line, $matches)) {
                $kode = $matches[1];
                $parts = explode('.', $kode);

                // Only 2-level codes (cities)
                if (count($parts) == 2) {
                    $provinceCode = $parts[0];
                    $provinceId = $this->provinceMap[$provinceCode] ?? null;

                    if ($provinceId) {
                        $cities[] = [
                            'wilayah_id' => $kode,
                            'name' => str_replace("''", "'", $matches[2]),
                            'province_id' => $provinceId,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                        $count++;

                        // Insert in batches of 100
                        if (count($cities) >= 100) {
                            DB::table('cities')->insert($cities);
                            $cities = [];
                        }
                    }
                }
            }
        }

        // Insert remaining cities
        if (!empty($cities)) {
            DB::table('cities')->insert($cities);
        }

        fclose($handle);

        // Build city lookup
        $this->cityMap = DB::table('cities')->pluck('id', 'wilayah_id')->toArray();
        $this->command->info("  ↳ Inserted {$count} cities");
    }

    private function processDistricts(string $sqlFile): void
    {
        $this->command->info('🏘️  Processing districts...');
        $districts = [];
        $count = 0;

        $handle = fopen($sqlFile, 'r');
        while (($line = fgets($handle)) !== false) {
            if (preg_match("/\('(\d+\.\d+\.\d+)','([^']+)'\)/", $line, $matches)) {
                $kode = $matches[1];
                $parts = explode('.', $kode);

                // Only 3-level codes (districts)
                if (count($parts) == 3) {
                    $cityCode = $parts[0] . '.' . $parts[1];
                    $cityId = $this->cityMap[$cityCode] ?? null;

                    if ($cityId) {
                        $districts[] = [
                            'wilayah_id' => $kode,
                            'name' => str_replace("''", "'", $matches[2]),
                            'city_id' => $cityId,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                        $count++;

                        // Insert in batches of 500
                        if (count($districts) >= 500) {
                            DB::table('districts')->insert($districts);
                            $districts = [];
                        }
                    }
                }
            }
        }

        // Insert remaining districts
        if (!empty($districts)) {
            DB::table('districts')->insert($districts);
        }

        fclose($handle);

        // Build district lookup
        $this->districtMap = DB::table('districts')->pluck('id', 'wilayah_id')->toArray();
        $this->command->info("  ↳ Inserted {$count} districts");
    }

    private function processVillages(string $sqlFile): void
    {
        $this->command->info('🏠 Processing villages...');
        $villages = [];
        $count = 0;

        $handle = fopen($sqlFile, 'r');
        while (($line = fgets($handle)) !== false) {
            if (preg_match("/\('(\d+\.\d+\.\d+\.\d+)','([^']+)'\)/", $line, $matches)) {
                $kode = $matches[1];
                $parts = explode('.', $kode);

                // Only 4-level codes (villages)
                if (count($parts) == 4) {
                    $districtCode = $parts[0] . '.' . $parts[1] . '.' . $parts[2];
                    $districtId = $this->districtMap[$districtCode] ?? null;

                    if ($districtId) {
                        $villages[] = [
                            'wilayah_id' => $kode,
                            'name' => str_replace("''", "'", $matches[2]),
                            'district_id' => $districtId,
                            'postal_code' => null,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                        $count++;

                        // Insert in batches of 1000
                        if (count($villages) >= 1000) {
                            DB::table('villages')->insert($villages);
                            $villages = [];

                            // Show progress every 1000 records
                            $this->command->info("  ↳ Processed {$count} villages...");
                        }
                    }
                }
            }
        }

        // Insert remaining villages
        if (!empty($villages)) {
            DB::table('villages')->insert($villages);
        }

        fclose($handle);
        $this->command->info("  ↳ Inserted {$count} villages");
    }

    private function showResults(): void
    {
        $this->command->table(['Table', 'Records'], [
            ['Provinces', DB::table('provinces')->count()],
            ['Cities', DB::table('cities')->count()],
            ['Districts', DB::table('districts')->count()],
            ['Villages', DB::table('villages')->count()],
        ]);
    }
}