<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;

class ProductImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();

        foreach ($products as $product) {
            // Check if product already has images
            if ($product->images()->count() > 0) {
                continue;
            }

            // Create a primary image for each product
            ProductImage::create([
                'product_id' => $product->id,
                'filename' => 'product-placeholder.png',
                'path' => 'products/product-placeholder.png',
                'url' => 'storage/products/product-placeholder.png',
                'alt_text' => $product->name,
                'is_primary' => true,
                'sort_order' => 1,
                'file_size' => 0, // Placeholder
                'mime_type' => 'image/png'
            ]);
        }

        $this->command->info('Product images seeded successfully!');
    }
}
