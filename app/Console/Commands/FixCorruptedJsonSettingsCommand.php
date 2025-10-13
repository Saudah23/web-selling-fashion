<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SystemSetting;

class FixCorruptedJsonSettingsCommand extends Command
{
    protected $signature = 'settings:fix-json {--dry-run : Show what would be fixed without making changes}';
    protected $description = 'Fix corrupted JSON settings caused by double encoding';

    public function handle()
    {
        $this->info('🔧 Fixing Corrupted JSON Settings');
        $this->info('==================================');

        $dryRun = $this->option('dry-run');
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        $jsonSettings = SystemSetting::where('type', 'json')->get();
        $fixedCount = 0;

        foreach ($jsonSettings as $setting) {
            $originalValue = $setting->value;
            $fixedValue = $this->fixJsonValue($originalValue);

            if ($originalValue !== $fixedValue) {
                $this->line("📝 {$setting->key}:");
                $this->line("   Before: " . substr($originalValue, 0, 100) . '...');
                $this->line("   After:  " . substr($fixedValue, 0, 100) . '...');

                if (!$dryRun) {
                    $setting->update(['value' => $fixedValue]);
                    SystemSetting::clearCache();
                }

                $fixedCount++;
            }
        }

        if ($fixedCount > 0) {
            if ($dryRun) {
                $this->info("✅ Found {$fixedCount} settings that need fixing");
                $this->line("Run without --dry-run to apply fixes");
            } else {
                $this->info("✅ Fixed {$fixedCount} corrupted JSON settings");
            }
        } else {
            $this->info("✅ No corrupted JSON settings found");
        }

        return 0;
    }

    private function fixJsonValue(string $value): string
    {
        // Keep trying to decode until we get to the original array
        $current = $value;
        $attempts = 0;
        $maxAttempts = 10; // Prevent infinite loop

        while ($attempts < $maxAttempts) {
            $decoded = json_decode($current, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                // Not valid JSON, stop here
                break;
            }

            if (is_array($decoded)) {
                // Successfully decoded to array, re-encode properly
                return json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            }

            if (is_string($decoded)) {
                // Decoded to another JSON string, try again
                $current = $decoded;
                $attempts++;
            } else {
                // Decoded to something else, stop
                break;
            }
        }

        // If we couldn't fix it, return original
        return $value;
    }
}