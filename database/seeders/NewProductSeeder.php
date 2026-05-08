<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class NewProductSeeder extends Seeder
{
    public function run(): void
    {
        // Bersihkan produk lama
        Product::query()->delete();

        // Tambah subkategori yang belum ada untuk wanita
        $wanitaParent = Category::where('name', 'Pakaian Wanita')->first()
            ?? Category::where('name', 'Pakaian')->whereNull('parent_id')->first();

        if ($wanitaParent) {
            $this->ensureSubcat($wanitaParent->id, 'Kaos Wanita', 'Kaos dan t-shirt wanita', 6);
            $this->ensureSubcat($wanitaParent->id, 'Kemeja Wanita', 'Kemeja dan atasan formal wanita', 7);
        }

        // Ambil semua subkategori dengan nama
        $cats = Category::whereNotNull('parent_id')->get()->keyBy('name');

        // Mapping produk ke subkategori
        $catBlouse   = $cats['Blouse']->id ?? null;
        $catKemejaW  = $cats['Kemeja Wanita']->id ?? $catBlouse;
        $catKaosW    = $cats['Kaos Wanita']->id ?? $catBlouse;

        $catKemejaP  = $cats['Kemeja']->id ?? null;
        $catKaosP    = $cats['Kaos']->id ?? null;
        $catJaket    = $cats['Jaket']->id ?? null;

        $catAnakL    = $cats['Baju Anak Laki-laki']->id ?? null;
        $catAnakP    = $cats['Baju Anak Perempuan']->id ?? null;

        $products = [
            // WANITA
            ['sku' => 'W-BL-001', 'name' => 'Blouse Floral Elegan',     'cat' => $catBlouse,  'price' => 120000, 'stok' => 10],
            ['sku' => 'W-KM-002', 'name' => 'Kemeja Wanita Putih',      'cat' => $catKemejaW, 'price' => 100000, 'stok' => 15],
            ['sku' => 'W-TN-003', 'name' => 'Tunik Muslimah Modern',    'cat' => $catBlouse,  'price' => 150000, 'stok' => 8],
            ['sku' => 'W-CR-004', 'name' => 'Crop Top Casual',          'cat' => $catKaosW,   'price' => 80000,  'stok' => 10],
            ['sku' => 'W-BL-005', 'name' => 'Blouse Satin Premium',     'cat' => $catBlouse,  'price' => 170000, 'stok' => 7],
            ['sku' => 'W-TS-006', 'name' => 'Kaos Wanita Polos',        'cat' => $catKaosW,   'price' => 50000,  'stok' => 10],
            ['sku' => 'W-KM-007', 'name' => 'Kemeja Denim Wanita',      'cat' => $catKemejaW, 'price' => 130000, 'stok' => 12],
            ['sku' => 'W-BL-008', 'name' => 'Blouse Lengan Balon',      'cat' => $catBlouse,  'price' => 140000, 'stok' => 9],
            ['sku' => 'W-RJ-009', 'name' => 'Atasan Rajut',             'cat' => $catBlouse,  'price' => 110000, 'stok' => 11],
            ['sku' => 'W-KM-010', 'name' => 'Kemeja Kotak Wanita',      'cat' => $catKemejaW, 'price' => 95000,  'stok' => 14],
            ['sku' => 'W-BL-011', 'name' => 'Blouse Korea Style',       'cat' => $catBlouse,  'price' => 135000, 'stok' => 10],
            ['sku' => 'W-TS-012', 'name' => 'Kaos Oversize Wanita',     'cat' => $catKaosW,   'price' => 85000,  'stok' => 5],
            ['sku' => 'W-RF-013', 'name' => 'Atasan Ruffle',            'cat' => $catBlouse,  'price' => 125000, 'stok' => 6],
            ['sku' => 'W-BT-014', 'name' => 'Blouse Batik Modern',      'cat' => $catBlouse,  'price' => 160000, 'stok' => 7],
            ['sku' => 'W-TK-015', 'name' => 'Tank Top Wanita',          'cat' => $catKaosW,   'price' => 60000,  'stok' => 22],
            ['sku' => 'W-KM-016', 'name' => 'Kemeja Kantor Wanita',     'cat' => $catKemejaW, 'price' => 145000, 'stok' => 9],
            ['sku' => 'W-BL-017', 'name' => 'Blouse Polkadot',          'cat' => $catBlouse,  'price' => 120000, 'stok' => 10],
            ['sku' => 'W-LC-018', 'name' => 'Atasan Lace',              'cat' => $catBlouse,  'price' => 155000, 'stok' => 5],
            ['sku' => 'W-TS-019', 'name' => 'Kaos Graphic Wanita',      'cat' => $catKaosW,   'price' => 70000,  'stok' => 10],
            ['sku' => 'W-BL-020', 'name' => 'Blouse Casual Simple',     'cat' => $catBlouse,  'price' => 90000,  'stok' => 17],

            // PRIA
            ['sku' => 'P-KM-001', 'name' => 'Kemeja Formal Pria',       'cat' => $catKemejaP, 'price' => 150000, 'stok' => 12],
            ['sku' => 'P-TS-002', 'name' => 'Kaos Polos Pria',          'cat' => $catKaosP,   'price' => 60000,  'stok' => 25],
            ['sku' => 'P-PL-003', 'name' => 'Polo Shirt Pria',          'cat' => $catKaosP,   'price' => 120000, 'stok' => 18],
            ['sku' => 'P-KM-004', 'name' => 'Kemeja Flanel',            'cat' => $catKemejaP, 'price' => 140000, 'stok' => 10],
            ['sku' => 'P-TS-005', 'name' => 'Kaos Graphic Pria',        'cat' => $catKaosP,   'price' => 75000,  'stok' => 20],
            ['sku' => 'P-KM-006', 'name' => 'Kemeja Denim Pria',        'cat' => $catKemejaP, 'price' => 155000, 'stok' => 8],
            ['sku' => 'P-TS-007', 'name' => 'Kaos Oversize Pria',       'cat' => $catKaosP,   'price' => 85000,  'stok' => 14],
            ['sku' => 'P-KM-008', 'name' => 'Kemeja Batik Pria',        'cat' => $catKemejaP, 'price' => 170000, 'stok' => 9],
            ['sku' => 'P-HD-009', 'name' => 'Hoodie Pria',              'cat' => $catJaket,   'price' => 180000, 'stok' => 7],
            ['sku' => 'P-SW-010', 'name' => 'Sweater Casual',           'cat' => $catJaket,   'price' => 160000, 'stok' => 11],
            ['sku' => 'P-KM-011', 'name' => 'Kemeja Slim Fit',          'cat' => $catKemejaP, 'price' => 145000, 'stok' => 10],
            ['sku' => 'P-SP-012', 'name' => 'Kaos Sport Pria',          'cat' => $catKaosP,   'price' => 90000,  'stok' => 16],
            ['sku' => 'P-KM-013', 'name' => 'Kemeja Linen',             'cat' => $catKemejaP, 'price' => 150000, 'stok' => 8],
            ['sku' => 'P-TS-014', 'name' => 'Kaos Lengan Panjang',      'cat' => $catKaosP,   'price' => 95000,  'stok' => 13],
            ['sku' => 'P-KM-015', 'name' => 'Kemeja Casual Pria',       'cat' => $catKemejaP, 'price' => 130000, 'stok' => 15],
            ['sku' => 'P-TK-016', 'name' => 'Tank Top Pria',            'cat' => $catKaosP,   'price' => 50000,  'stok' => 20],
            ['sku' => 'P-KM-017', 'name' => 'Kemeja Motif',             'cat' => $catKemejaP, 'price' => 140000, 'stok' => 9],
            ['sku' => 'P-TS-018', 'name' => 'Kaos Streetwear',          'cat' => $catKaosP,   'price' => 100000, 'stok' => 18],
            ['sku' => 'P-SW-019', 'name' => 'Sweater Rajut',            'cat' => $catJaket,   'price' => 170000, 'stok' => 6],
            ['sku' => 'P-HD-020', 'name' => 'Hoodie Zip',               'cat' => $catJaket,   'price' => 185000, 'stok' => 5],

            // ANAK
            ['sku' => 'A-TS-001', 'name' => 'Kaos Anak Kartun',         'cat' => $catAnakL,   'price' => 50000,  'stok' => 20],
            ['sku' => 'A-KM-002', 'name' => 'Kemeja Anak Lucu',         'cat' => $catAnakL,   'price' => 70000,  'stok' => 15],
            ['sku' => 'A-TS-003', 'name' => 'Kaos Polos Anak',          'cat' => $catAnakL,   'price' => 45000,  'stok' => 25],
            ['sku' => 'A-HD-004', 'name' => 'Hoodie Anak',              'cat' => $catAnakL,   'price' => 90000,  'stok' => 10],
            ['sku' => 'A-SW-005', 'name' => 'Sweater Anak',             'cat' => $catAnakL,   'price' => 85000,  'stok' => 12],
            ['sku' => 'A-TS-006', 'name' => 'Kaos Lengan Panjang Anak', 'cat' => $catAnakL,   'price' => 60000,  'stok' => 18],
            ['sku' => 'A-KM-007', 'name' => 'Kemeja Denim Anak',        'cat' => $catAnakL,   'price' => 95000,  'stok' => 8],
            ['sku' => 'A-SP-008', 'name' => 'Kaos Sport Anak',          'cat' => $catAnakL,   'price' => 55000,  'stok' => 16],
            ['sku' => 'A-TK-009', 'name' => 'Tank Top Anak',            'cat' => $catAnakL,   'price' => 40000,  'stok' => 22],
            ['sku' => 'A-TS-010', 'name' => 'Kaos Motif Hewan',         'cat' => $catAnakP,   'price' => 65000,  'stok' => 14],
            ['sku' => 'A-KM-011', 'name' => 'Kemeja Kotak Anak',        'cat' => $catAnakL,   'price' => 80000,  'stok' => 10],
            ['sku' => 'A-TS-012', 'name' => 'Kaos Warna Cerah',         'cat' => $catAnakP,   'price' => 50000,  'stok' => 19],
            ['sku' => 'A-HD-013', 'name' => 'Hoodie Kartun',            'cat' => $catAnakP,   'price' => 95000,  'stok' => 9],
            ['sku' => 'A-SW-014', 'name' => 'Sweater Rajut Anak',       'cat' => $catAnakP,   'price' => 88000,  'stok' => 7],
            ['sku' => 'A-TS-015', 'name' => 'Kaos Superhero',           'cat' => $catAnakL,   'price' => 70000,  'stok' => 15],
            ['sku' => 'A-KM-016', 'name' => 'Kemeja Formal Anak',       'cat' => $catAnakL,   'price' => 90000,  'stok' => 8],
            ['sku' => 'A-TS-017', 'name' => 'Kaos Casual Anak',         'cat' => $catAnakP,   'price' => 50000,  'stok' => 20],
            ['sku' => 'A-TS-018', 'name' => 'Kaos Stripe Anak',         'cat' => $catAnakP,   'price' => 55000,  'stok' => 13],
            ['sku' => 'A-HD-019', 'name' => 'Hoodie Simple Anak',       'cat' => $catAnakP,   'price' => 85000,  'stok' => 11],
            ['sku' => 'A-TS-020', 'name' => 'Kaos Lucu Anak',           'cat' => $catAnakP,   'price' => 60000,  'stok' => 17],
        ];

        foreach ($products as $i => $data) {
            if (!$data['cat']) continue;
            Product::create([
                'name'              => $data['name'],
                'sku'               => $data['sku'],
                'description'       => $data['name'] . ' berkualitas tinggi dari FASHION SAAZZ. Bahan pilihan, nyaman dipakai sehari-hari.',
                'short_description' => $data['name'] . ' - pilihan terbaik.',
                'price'             => $data['price'],
                'compare_price'     => null,
                'stock_quantity'    => $data['stok'],
                'min_stock_level'   => 3,
                'weight'            => 0.3,
                'is_active'         => true,
                'is_featured'       => $i < 6,
                'sort_order'        => $i + 1,
                'category_id'       => $data['cat'],
            ]);
        }

        $this->command->info('60 produk baru berhasil dimasukkan!');
    }

    private function ensureSubcat(int $parentId, string $name, string $desc, int $order): void
    {
        Category::firstOrCreate(
            ['name' => $name, 'parent_id' => $parentId],
            ['description' => $desc, 'is_active' => true, 'sort_order' => $order]
        );
    }
}
