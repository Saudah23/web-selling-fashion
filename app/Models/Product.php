<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'sku',
        'price',
        'compare_price',
        'stock_quantity',
        'min_stock_level',
        'weight',
        'dimensions',
        'is_active',
        'is_featured',
        'sort_order',
        'attributes',
        'category_id'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'attributes' => 'array',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->ordered();
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    // Mutators
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    // Accessors
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getFormattedComparePriceAttribute()
    {
        return $this->compare_price ? 'Rp ' . number_format($this->compare_price, 0, ',', '.') : null;
    }

    public function getDiscountPercentageAttribute()
    {
        if ($this->compare_price && $this->compare_price > $this->price) {
            return round((($this->compare_price - $this->price) / $this->compare_price) * 100);
        }
        return 0;
    }

    public function getMainImageAttribute()
    {
        return $this->primaryImage ?: $this->images->first();
    }

    public function getMainImageUrlAttribute()
    {
        $mainImage = $this->getMainImageAttribute();
        return $mainImage ? $mainImage->full_url : null;
    }

    public function getIsLowStockAttribute()
    {
        return $this->stock_quantity <= $this->min_stock_level;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'min_stock_level');
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('sku', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    // Validation Rules
    public static function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'category_id' => [
                'required',
                'exists:categories,id',
                function ($_, $value, $fail) {
                    $category = Category::find($value);
                    if ($category && $category->parent_id === null) {
                        $fail('Products must be assigned to subcategories only, not parent categories.');
                    }
                }
            ],
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
        ];
    }

    // Helper method to check if category is valid (subcategory)
    public function isValidCategory($categoryId)
    {
        $category = Category::find($categoryId);
        return $category && $category->parent_id !== null;
    }

    // Events
    protected static function boot()
    {
        parent::boot();

        // Delete all related images when product is deleted
        static::deleting(function ($product) {
            $product->images()->each(function ($image) {
                $image->delete(); // This will trigger ProductImage delete() method to remove files
            });
        });

        // Validate category before saving
        static::saving(function ($product) {
            if ($product->category_id) {
                $category = Category::find($product->category_id);
                if ($category && $category->parent_id === null) {
                    throw new \Exception('Products can only be assigned to subcategories, not parent categories.');
                }
            }
        });
    }
}
