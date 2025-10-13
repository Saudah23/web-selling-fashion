<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'image',
        'button_text',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get active banners ordered by sort_order
     */
    public static function active()
    {
        return static::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get image URL
     */
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return asset('furni-1.0.0/images/couch.png'); // Default fallback
        }

        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        return Storage::url($this->image);
    }

    /**
     * Delete image when banner is deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($banner) {
            if ($banner->image && !filter_var($banner->image, FILTER_VALIDATE_URL)) {
                Storage::delete($banner->image);
            }
        });
    }

}