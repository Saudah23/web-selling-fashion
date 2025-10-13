<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerAddress extends Model
{
    protected $fillable = [
        'user_id',
        'label',
        'is_default',
        'recipient_name',
        'recipient_phone',
        'province_id',
        'city_id',
        'district_id',
        'village_id',
        'address_detail',
        'postal_code',
        'notes',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    // Scopes
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Accessors
    public function getFullAddressAttribute(): string
    {
        return implode(', ', array_filter([
            $this->address_detail,
            $this->village->name ?? null,
            $this->district->name ?? null,
            $this->city->name ?? null,
            $this->province->name ?? null,
            $this->postal_code,
        ]));
    }

    // Methods
    public function makeDefault(): void
    {
        // Remove default from other addresses for this user
        static::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        // Set this address as default
        $this->update(['is_default' => true]);
    }

    // Boot method to handle default address logic
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($address) {
            // If this is the first address for user, make it default
            $existingCount = static::where('user_id', $address->user_id)->count();
            if ($existingCount === 0) {
                $address->is_default = true;
            }
        });

        static::updating(function ($address) {
            // If setting as default, remove default from others
            if ($address->is_default && $address->isDirty('is_default')) {
                static::where('user_id', $address->user_id)
                    ->where('id', '!=', $address->id)
                    ->update(['is_default' => false]);
            }
        });
    }
}
