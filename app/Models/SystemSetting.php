<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SystemSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
        'is_public'
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * Get a setting value by key with caching
     */
    public static function get(string $key, $default = null)
    {
        try {
            return Cache::remember("setting.{$key}", 3600, function () use ($key, $default) {
                $setting = static::where('key', $key)->first();

                if (!$setting) {
                    return $default;
                }

                return static::castValue($setting->value, $setting->type);
            });
        } catch (\Exception $e) {
            // Return default if database/cache is not available (e.g., during migration)
            return $default;
        }
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, $value, string $type = 'text'): void
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => static::prepareValue($value, $type),
                'type' => $type
            ]
        );

        Cache::forget("setting.{$key}");
    }

    /**
     * Get settings by group
     */
    public static function getGroup(string $group): array
    {
        return Cache::remember("settings.group.{$group}", 3600, function () use ($group) {
            return static::where('group', $group)
                ->get()
                ->mapWithKeys(function ($setting) {
                    return [
                        $setting->key => static::castValue($setting->value, $setting->type)
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Get public settings for frontend
     */
    public static function getPublic(): array
    {
        return Cache::remember('settings.public', 3600, function () {
            return static::where('is_public', true)
                ->get()
                ->mapWithKeys(function ($setting) {
                    return [
                        $setting->key => static::castValue($setting->value, $setting->type)
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Cast value based on type
     */
    protected static function castValue($value, string $type)
    {
        return match ($type) {
            'json' => json_decode($value, true),
            'boolean' => (bool) $value,
            'number' => is_numeric($value) ? (float) $value : $value,
            'integer' => (int) $value,
            default => $value
        };
    }

    /**
     * Prepare value for storage
     */
    protected static function prepareValue($value, string $type): string
    {
        return match ($type) {
            'json' => static::prepareJsonValue($value),
            'boolean' => $value ? '1' : '0',
            default => (string) $value
        };
    }

    /**
     * Properly handle JSON value preparation
     */
    protected static function prepareJsonValue($value): string
    {
        // If value is already a JSON string, decode it first to avoid double encoding
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                // It's valid JSON, use the decoded value
                return json_encode($decoded, JSON_UNESCAPED_SLASHES);
            }
        }

        // If it's an array or object, encode it normally
        if (is_array($value) || is_object($value)) {
            return json_encode($value, JSON_UNESCAPED_SLASHES);
        }

        // For other types, convert to string
        return (string) $value;
    }

    /**
     * Clear all settings cache
     */
    public static function clearCache(): void
    {
        Cache::flush(); // or use a more specific cache pattern
    }

    /**
     * Get shipping origin location details with names
     */
    public static function getShippingOriginDetails(): array
    {
        $provinceId = static::get('shipping_origin_province_id');
        $cityId = static::get('shipping_origin_city_id');
        $districtId = static::get('shipping_origin_district_id');

        $details = [
            'province' => null,
            'city' => null,
            'district' => null,
            'address' => static::get('shipping_origin_address', '')
        ];

        if ($provinceId) {
            $province = \App\Models\Province::find($provinceId);
            if ($province) {
                $details['province'] = [
                    'id' => $province->id,
                    'name' => $province->name,
                    'wilayah_id' => $province->wilayah_id,
                    'rajaongkir_id' => $province->rajaongkir_id
                ];
            }
        }

        if ($cityId) {
            $city = \App\Models\City::find($cityId);
            if ($city) {
                $details['city'] = [
                    'id' => $city->id,
                    'name' => $city->name,
                    'wilayah_id' => $city->wilayah_id,
                    'rajaongkir_id' => $city->rajaongkir_id,
                    'rajaongkir_type' => $city->rajaongkir_type
                ];
            }
        }

        if ($districtId) {
            $district = \App\Models\District::find($districtId);
            if ($district) {
                $details['district'] = [
                    'id' => $district->id,
                    'name' => $district->name,
                    'wilayah_id' => $district->wilayah_id
                ];
            }
        }

        return $details;
    }

    /**
     * Set shipping origin location and auto-sync RajaOngkir ID
     */
    public static function setShippingOrigin(int $provinceId, int $cityId, int $districtId, string $address = ''): bool
    {
        try {
            // Validate relationships
            $province = \App\Models\Province::find($provinceId);
            $city = \App\Models\City::where('id', $cityId)->where('province_id', $provinceId)->first();
            $district = \App\Models\District::where('id', $districtId)->where('city_id', $cityId)->first();

            if (!$province || !$city || !$district) {
                throw new \InvalidArgumentException('Invalid location hierarchy');
            }

            // Set all shipping origin settings
            static::set('shipping_origin_province_id', $provinceId, 'number');
            static::set('shipping_origin_city_id', $cityId, 'number');
            static::set('shipping_origin_district_id', $districtId, 'number');
            static::set('shipping_origin_address', $address, 'text');

            // Auto-sync RajaOngkir ID if available
            if ($city->rajaongkir_id) {
                static::set('rajaongkir_origin_city_id', $city->rajaongkir_id, 'number');
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to set shipping origin: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Boot method to clear cache on model events
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($setting) {
            Cache::forget("setting.{$setting->key}");
            Cache::forget("settings.group.{$setting->group}");
            if ($setting->is_public) {
                Cache::forget('settings.public');
            }
        });

        static::deleted(function ($setting) {
            Cache::forget("setting.{$setting->key}");
            Cache::forget("settings.group.{$setting->group}");
            Cache::forget('settings.public');
        });
    }
}