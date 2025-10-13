<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Village extends Model
{
    protected $fillable = [
        'wilayah_id',
        'name',
        'district_id',
        'postal_code',
    ];

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function customerAddresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function scopeByDistrict($query, $districtId)
    {
        return $query->where('district_id', $districtId);
    }

    public function scopeWithPostalCode($query)
    {
        return $query->whereNotNull('postal_code');
    }
}
