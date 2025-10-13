<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'transaction_id',
        'payment_type',
        'gross_amount',
        'currency',
        'status',
        'fraud_status',
        'transaction_time',
        'settlement_time',
        'midtrans_response',
        'customer_details',
        'item_details',
        'shipping_address',
        'shipping_cost',
        'shipping_service',
        'payment_url',
        'pdf_url',
        'notes',
        'metadata'
    ];

    protected $casts = [
        'gross_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'transaction_time' => 'datetime',
        'settlement_time' => 'datetime',
        'midtrans_response' => 'array',
        'customer_details' => 'array',
        'item_details' => 'array',
        'shipping_address' => 'array',
        'metadata' => 'array'
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_number');
    }

    // Accessors
    public function getFormattedGrossAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->gross_amount, 0, ',', '.');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'settlement' => '<span class="badge bg-success">Settlement</span>',
            'capture' => '<span class="badge bg-info">Capture</span>',
            'deny' => '<span class="badge bg-danger">Deny</span>',
            'cancel' => '<span class="badge bg-secondary">Cancel</span>',
            'expire' => '<span class="badge bg-dark">Expire</span>',
            'failure' => '<span class="badge bg-danger">Failure</span>',
            'refund' => '<span class="badge bg-warning">Refund</span>',
            'partial_refund' => '<span class="badge bg-warning">Partial Refund</span>',
            default => '<span class="badge bg-light">Unknown</span>'
        };
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeSuccessful($query)
    {
        return $query->whereIn('status', ['settlement', 'capture']);
    }

    // Methods
    public function isSuccessful(): bool
    {
        return in_array($this->status, ['settlement', 'capture']);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isFailed(): bool
    {
        return in_array($this->status, ['deny', 'cancel', 'expire', 'failure']);
    }
}