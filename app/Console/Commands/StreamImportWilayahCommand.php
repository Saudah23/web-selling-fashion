<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Database\Seeders\StreamingWilayahSeeder;

class StreamImportWilayahCommand extends Command
{
    protected $signature = 'wilayah:stream-import {--fresh : Clear existing data}';

    protected $description = 'Memory-efficient streaming import of wilayah data';

    public function handle()
    {
        $this->info('🌊 Streaming Wilayah Import (Memory Efficient)');
        $this->info('==============================================');

        if ($this->option('fresh')) {
            if (!$this->confirm('Delete existing data?', false)) {
                return 0;
            }
        }

        // Check file exists
        if (!file_exists(database_path('wilayah.sql'))) {
            $this->error('❌ wilayah.sql not found!');
            return 1;
        }

        // Check memory limit
        $memoryLimit = ini_get('memory_limit');
        $this->info("💾 PHP Memory limit: {$memoryLimit}");

        try {
            $seeder = new StreamingWilayahSeeder();
            $seeder->setCommand($this);
            $seeder->run();

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Import failed: ' . $e->getMessage());
            return 1;
        }
    }
}