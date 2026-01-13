<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all subcategories (tier 2) - these are the only categories products can be assigned to
        $subcategories = Category::whereNotNull('parent_id')->where('is_active', true)->get();

        // Group subcategories by parent for easier access
        $categoryMap = [];
        foreach ($subcategories as $subcat) {
            $categoryMap[$subcat->slug] = $subcat->id;
        }

        // Sample product data - using only subcategories
        $products = [
            // Men's Clothing - Kemeja
            [
                'name' => 'Classic Oxford Shirt',
                'description' => 'Premium cotton oxford shirt with classic fit. Perfect for both formal and casual occasions. Features button-down collar and chest pocket.',
                'short_description' => 'Premium cotton oxford shirt with classic fit',
                'sku' => 'MEN-OXF-001',
                'price' => 299000,
                'compare_price' => 399000,
                'stock_quantity' => 25,
                'min_stock_level' => 5,
                'weight' => 0.3,
                'dimensions' => 'S, M, L, XL, XXL',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 1,
                'category_id' => $categoryMap['kemeja'] ?? null,
                'attributes' => [
                    'material' => 'Cotton',
                    'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                    'colors' => ['White', 'Light Blue', 'Navy'],
                    'care_instructions' => 'Machine wash cold, tumble dry low'
                ]
            ],
            [
                'name' => 'Formal Business Shirt',
                'description' => 'Professional business shirt with sharp collar and premium finish. Ideal for office and formal events.',
                'short_description' => 'Professional business shirt',
                'sku' => 'MEN-BUS-002',
                'price' => 349000,
                'stock_quantity' => 20,
                'min_stock_level' => 5,
                'weight' => 0.3,
                'dimensions' => 'S, M, L, XL, XXL',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 2,
                'category_id' => $categoryMap['kemeja'] ?? null,
                'attributes' => [
                    'material' => 'Cotton Blend',
                    'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                    'colors' => ['White', 'Blue', 'Light Grey']
                ]
            ],

            // Men's Clothing - Celana
            [
                'name' => 'Slim Fit Chino Pants',
                'description' => 'Comfortable slim-fit chino pants made from premium cotton twill. Versatile design suitable for work or weekend wear.',
                'short_description' => 'Comfortable slim-fit chino pants',
                'sku' => 'MEN-CHI-003',
                'price' => 349000,
                'stock_quantity' => 30,
                'min_stock_level' => 5,
                'weight' => 0.4,
                'dimensions' => '28-38 waist',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 3,
                'category_id' => $categoryMap['celana'] ?? null,
                'attributes' => [
                    'material' => 'Cotton Twill',
                    'sizes' => ['28', '30', '32', '34', '36', '38'],
                    'colors' => ['Khaki', 'Navy', 'Black', 'Olive'],
                    'fit' => 'Slim'
                ]
            ],

            // Men's Clothing - Jaket
            [
                'name' => 'Denim Jacket',
                'description' => 'Classic denim jacket with vintage wash. Perfect layering piece for casual and street style looks.',
                'short_description' => 'Classic denim jacket with vintage wash',
                'sku' => 'MEN-DEN-004',
                'price' => 459000,
                'compare_price' => 599000,
                'stock_quantity' => 18,
                'min_stock_level' => 3,
                'weight' => 0.8,
                'dimensions' => 'S, M, L, XL, XXL',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 4,
                'category_id' => $categoryMap['jaket'] ?? null,
                'attributes' => [
                    'material' => 'Denim',
                    'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                    'colors' => ['Blue Wash', 'Dark Blue', 'Black']
                ]
            ],

            // Men's Clothing - Kaos
            [
                'name' => 'Limited Edition T-Shirt',
                'description' => 'Limited edition graphic t-shirt with premium cotton fabric. Unique design available for a short time only.',
                'short_description' => 'Limited edition graphic t-shirt',
                'sku' => 'LIM-TSH-005',
                'price' => 199000,
                'stock_quantity' => 2,
                'min_stock_level' => 5,
                'weight' => 0.2,
                'dimensions' => 'S, M, L, XL',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 5,
                'category_id' => $categoryMap['kaos'] ?? null,
                'attributes' => [
                    'material' => 'Premium Cotton',
                    'sizes' => ['S', 'M', 'L', 'XL'],
                    'colors' => ['Black'],
                    'edition' => 'Limited',
                    'print' => 'Graphic'
                ]
            ],

            // Men's Clothing - Sepatu
            [
                'name' => 'Canvas Sneakers',
                'description' => 'Comfortable canvas sneakers with rubber sole. Classic design suitable for casual wear and everyday activities.',
                'short_description' => 'Comfortable canvas sneakers',
                'sku' => 'SHO-SNE-006',
                'price' => 259000,
                'compare_price' => 329000,
                'stock_quantity' => 40,
                'min_stock_level' => 8,
                'weight' => 0.6,
                'dimensions' => 'EU 36-45',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 6,
                'category_id' => $categoryMap['sepatu'] ?? null,
                'attributes' => [
                    'material' => 'Canvas',
                    'sole' => 'Rubber',
                    'sizes' => ['36', '37', '38', '39', '40', '41', '42', '43', '44', '45'],
                    'colors' => ['White', 'Black', 'Navy', 'Red'],
                    'style' => 'Casual'
                ]
            ],

            // Women's Clothing - Dress
            [
                'name' => 'Floral Midi Dress',
                'description' => 'Elegant floral midi dress with a flattering A-line silhouette. Made from soft, breathable fabric perfect for any season.',
                'short_description' => 'Elegant floral midi dress with A-line silhouette',
                'sku' => 'WOM-DRS-007',
                'price' => 459000,
                'compare_price' => 599000,
                'stock_quantity' => 20,
                'min_stock_level' => 3,
                'weight' => 0.3,
                'dimensions' => 'XS, S, M, L, XL',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 7,
                'category_id' => $categoryMap['dress'] ?? null,
                'attributes' => [
                    'material' => 'Viscose blend',
                    'sizes' => ['XS', 'S', 'M', 'L', 'XL'],
                    'colors' => ['Floral Pink', 'Floral Blue'],
                    'length' => 'Midi',
                    'occasion' => 'Casual, Semi-formal'
                ]
            ],

            // Women's Clothing - Blouse
            [
                'name' => 'Silk Blouse',
                'description' => 'Elegant silk blouse with delicate button details. Perfect for office wear or special occasions.',
                'short_description' => 'Elegant silk blouse with button details',
                'sku' => 'WOM-BLO-008',
                'price' => 389000,
                'stock_quantity' => 25,
                'min_stock_level' => 5,
                'weight' => 0.2,
                'dimensions' => 'XS, S, M, L, XL',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 8,
                'category_id' => $categoryMap['blouse'] ?? null,
                'attributes' => [
                    'material' => 'Silk',
                    'sizes' => ['XS', 'S', 'M', 'L', 'XL'],
                    'colors' => ['White', 'Cream', 'Light Pink']
                ]
            ],

            // Women's Clothing - Rok
            [
                'name' => 'Pleated Midi Skirt',
                'description' => 'Classic pleated midi skirt with elastic waistband. Versatile piece for both casual and formal styling.',
                'short_description' => 'Classic pleated midi skirt',
                'sku' => 'WOM-SKI-009',
                'price' => 279000,
                'stock_quantity' => 22,
                'min_stock_level' => 4,
                'weight' => 0.3,
                'dimensions' => 'XS, S, M, L, XL',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 9,
                'category_id' => $categoryMap['rok'] ?? null,
                'attributes' => [
                    'material' => 'Polyester blend',
                    'sizes' => ['XS', 'S', 'M', 'L', 'XL'],
                    'colors' => ['Black', 'Navy', 'Burgundy']
                ]
            ],

            // Women's Clothing - Celana Wanita
            [
                'name' => 'High-Waist Skinny Jeans',
                'description' => 'Classic high-waist skinny jeans with stretch denim for comfort and style. Features five-pocket design and ankle length.',
                'short_description' => 'High-waist skinny jeans with stretch denim',
                'sku' => 'WOM-JEA-010',
                'price' => 389000,
                'stock_quantity' => 35,
                'min_stock_level' => 5,
                'weight' => 0.5,
                'dimensions' => '24-32 waist',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 10,
                'category_id' => $categoryMap['celana-wanita'] ?? null,
                'attributes' => [
                    'material' => 'Stretch Denim',
                    'sizes' => ['24', '26', '28', '30', '32'],
                    'colors' => ['Dark Blue', 'Black', 'Light Blue'],
                    'fit' => 'Skinny',
                    'rise' => 'High-waist'
                ]
            ],

            // Women's Clothing - Sepatu Wanita
            [
                'name' => 'Block Heel Pumps',
                'description' => 'Comfortable block heel pumps perfect for office wear. Features cushioned insole and stable heel for all-day comfort.',
                'short_description' => 'Comfortable block heel pumps',
                'sku' => 'WOM-PUM-011',
                'price' => 449000,
                'stock_quantity' => 18,
                'min_stock_level' => 3,
                'weight' => 0.8,
                'dimensions' => 'EU 35-42',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 11,
                'category_id' => $categoryMap['sepatu-wanita'] ?? null,
                'attributes' => [
                    'heel_height' => '5cm',
                    'sizes' => ['35', '36', '37', '38', '39', '40', '41', '42'],
                    'colors' => ['Black', 'Nude', 'Navy']
                ]
            ],



            // Kids - Baju Anak Laki-laki
            [
                'name' => 'Boys Polo Shirt',
                'description' => 'Comfortable cotton polo shirt for boys. Perfect for school, play, and casual outings.',
                'short_description' => 'Comfortable cotton polo shirt for boys',
                'sku' => 'KID-BOY-017',
                'price' => 129000,
                'stock_quantity' => 40,
                'min_stock_level' => 8,
                'weight' => 0.2,
                'dimensions' => 'Ages 4-14',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 17,
                'category_id' => $categoryMap['baju-anak-laki-laki'] ?? null,
                'attributes' => [
                    'material' => 'Cotton',
                    'sizes' => ['4Y', '6Y', '8Y', '10Y', '12Y', '14Y'],
                    'colors' => ['Navy', 'White', 'Red', 'Green']
                ]
            ],

            // Kids - Baju Anak Perempuan
            [
                'name' => 'Girls Floral Dress',
                'description' => 'Adorable floral dress for girls with comfortable cotton fabric. Perfect for parties and special occasions.',
                'short_description' => 'Adorable floral dress for girls',
                'sku' => 'KID-GIR-018',
                'price' => 159000,
                'stock_quantity' => 35,
                'min_stock_level' => 7,
                'weight' => 0.2,
                'dimensions' => 'Ages 3-12',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 18,
                'category_id' => $categoryMap['baju-anak-perempuan'] ?? null,
                'attributes' => [
                    'material' => 'Cotton',
                    'sizes' => ['3Y', '4Y', '6Y', '8Y', '10Y', '12Y'],
                    'colors' => ['Pink Floral', 'Blue Floral', 'Yellow Floral']
                ]
            ],

            // Kids - Sepatu Anak
            [
                'name' => 'Kids Sports Shoes',
                'description' => 'Comfortable sports shoes for kids with non-slip sole. Perfect for running, playing, and everyday activities.',
                'short_description' => 'Comfortable sports shoes for kids',
                'sku' => 'KID-SHO-019',
                'price' => 199000,
                'stock_quantity' => 45,
                'min_stock_level' => 9,
                'weight' => 0.4,
                'dimensions' => 'EU 25-35',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 19,
                'category_id' => $categoryMap['sepatu-anak'] ?? null,
                'attributes' => [
                    'sizes' => ['25', '26', '27', '28', '29', '30', '31', '32', '33', '34', '35'],
                    'colors' => ['Blue/White', 'Pink/White', 'Black/Red'],
                    'sole_type' => 'Non-slip'
                ]
            ]
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }

        $this->command->info('Products seeded successfully!');
    }
}
