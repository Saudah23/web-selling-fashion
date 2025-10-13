<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_sku',
        'product_price',
        'product_compare_price',
        'product_image',
        'quantity',
        'subtotal',
        'product_attributes'
    ];

    protected $casts = [
        'product_price' => 'decimal:2',
        'product_compare_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'quantity' => 'integer',
        'product_attributes' => 'array'
    ];

    // Relationships
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Accessors
    public function getFormattedProductPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->product_price, 0, ',', '.');
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    public function getDiscountPercentageAttribute(): ?int
    {
        if ($this->product_compare_price && $this->product_compare_price > $this->product_price) {
            return round((($this->product_compare_price - $this->product_price) / $this->product_compare_price) * 100);
        }
        return null;
    }

    // Methods
    public function calculateSubtotal(): void
    {
        $this->subtotal = $this->quantity * $this->product_price;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($orderItem) {
            $orderItem->calculateSubtotal();
        });

        static::updating(function ($orderItem) {
            if ($orderItem->isDirty(['quantity', 'product_price'])) {
                $orderItem->calculateSubtotal();
            }
        });
    }
}