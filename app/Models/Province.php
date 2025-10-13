<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Province extends Model
{
    protected $fillable = [
        'wilayah_id',
        'name',
        'rajaongkir_id',
    ];

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    public function customerAddresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function scopeWithRajaOngkir($query)
    {
        return $query->whereNotNull('rajaongkir_id');
    }
}
