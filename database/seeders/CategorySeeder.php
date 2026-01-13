<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Parent Categories
            [
                'name' => 'Pakaian Pria',
                'description' => 'Koleksi fashion pria terlengkap',
                'is_active' => true,
                'sort_order' => 1,
                'parent_id' => null,
                'children' => [
                    ['name' => 'Kemeja', 'description' => 'Kemeja pria berbagai model', 'sort_order' => 1],
                    ['name' => 'Celana', 'description' => 'Celana pria casual dan formal', 'sort_order' => 2],
                    ['name' => 'Jaket', 'description' => 'Jaket dan outerwear pria', 'sort_order' => 3],
                    ['name' => 'Kaos', 'description' => 'T-shirt dan kaos pria', 'sort_order' => 4],
                    ['name' => 'Sepatu', 'description' => 'Sepatu pria berbagai jenis', 'sort_order' => 5],
                ]
            ],
            [
                'name' => 'Pakaian',
                'description' => 'Koleksi fashion wanita terlengkap',
                'is_active' => true,
                'sort_order' => 2,
                'parent_id' => null,
                'children' => [
                    ['name' => 'Dress', 'description' => 'Dress wanita berbagai model', 'sort_order' => 1],
                    ['name' => 'Blouse', 'description' => 'Blouse dan atasan wanita', 'sort_order' => 2],
                    ['name' => 'Rok', 'description' => 'Rok wanita berbagai panjang', 'sort_order' => 3],
                    ['name' => 'Celana Wanita', 'description' => 'Celana wanita casual dan formal', 'sort_order' => 4],
                    ['name' => 'Sepatu Wanita', 'description' => 'Sepatu wanita berbagai jenis', 'sort_order' => 5],
                ]
            ],
            [
                'name' => 'Pakaian Anak',
                'description' => 'Fashion anak-anak',
                'is_active' => true,
                'sort_order' => 3,
                'parent_id' => null,
                'children' => [
                    ['name' => 'Baju Anak Laki-laki', 'description' => 'Pakaian anak laki-laki', 'sort_order' => 1],
                    ['name' => 'Baju Anak Perempuan', 'description' => 'Pakaian anak perempuan', 'sort_order' => 2],
                    ['name' => 'Sepatu Anak', 'description' => 'Sepatu untuk anak-anak', 'sort_order' => 3],
                ]
            ]
        ];

        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);

            $parent = Category::create($categoryData);

            foreach ($children as $childData) {
                $childData['parent_id'] = $parent->id;
                $childData['is_active'] = true;
                Category::create($childData);
            }
        }
    }
}
