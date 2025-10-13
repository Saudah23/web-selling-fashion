<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    protected $fillable = [
        'wilayah_id',
        'name',
        'province_id',
        'rajaongkir_id',
        'rajaongkir_type',
    ];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function districts(): HasMany
    {
        return $this->hasMany(District::class);
    }

    public function customerAddresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function scopeWithRajaOngkir($query)
    {
        return $query->whereNotNull('rajaongkir_id');
    }

    public function scopeByProvince($query, $provinceId)
    {
        return $query->where('province_id', $provinceId);
    }
}
