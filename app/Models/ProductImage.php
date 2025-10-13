<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Services\ImageProcessingService;

class ProductImage extends Model
{
    protected $fillable = [
        'product_id',
        'filename',
        'path',
        'url',
        'alt_text',
        'is_primary',
        'sort_order',
        'file_size',
        'mime_type',
        'metadata'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'metadata' => 'array',
        'file_size' => 'integer'
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Scopes
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at');
    }

    // Accessors
    public function getFormattedFileSizeAttribute()
    {
        if (!$this->file_size) return null;

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getFullUrlAttribute()
    {
        return $this->url ? asset($this->url) : null;
    }

    // Methods
    public function delete()
    {
        // Delete physical file when model is deleted
        $imageService = app(ImageProcessingService::class);

        if ($this->metadata && isset($this->metadata['path'])) {
            // Use service to delete with metadata
            $imageService->deleteProcessedImage($this->metadata);
        } else {
            // Fallback: delete single file directly
            if ($this->path && Storage::disk('public')->exists($this->path)) {
                Storage::disk('public')->delete($this->path);
            }
        }

        return parent::delete();
    }

    // Events
    protected static function boot()
    {
        parent::boot();

        // When setting a new primary image, unset others for same product
        static::creating(function ($image) {
            if ($image->is_primary) {
                static::where('product_id', $image->product_id)
                    ->where('is_primary', true)
                    ->update(['is_primary' => false]);
            }
        });

        static::updating(function ($image) {
            if ($image->isDirty('is_primary') && $image->is_primary) {
                static::where('product_id', $image->product_id)
                    ->where('id', '!=', $image->id)
                    ->where('is_primary', true)
                    ->update(['is_primary' => false]);
            }
        });
    }
}
