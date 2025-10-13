<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Banner;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banners = [
            [
                'title' => 'Koleksi Fashion Terbaru',
                'subtitle' => 'Gaya Terkini untuk Anda',
                'description' => 'Temukan koleksi fashion terbaru dengan kualitas premium dan harga terjangkau. Ekspresikan gaya Anda dengan pilihan busana trendy dari brand ternama.',
                'image' => 'banners/placeholder.jpg',
                'button_text' => 'Belanja Sekarang',
                'is_active' => true,
                'sort_order' => 1
            ],
            [
                'title' => 'Elektronik & Gadget',
                'subtitle' => 'Teknologi Terdepan',
                'description' => 'Dapatkan gadget dan elektronik terbaru dengan teknologi canggih. Smartphone, laptop, dan aksesoris teknologi dengan harga spesial.',
                'image' => 'banners/placeholder.jpg',
                'button_text' => 'Jelajahi Produk',
                'is_active' => true,
                'sort_order' => 2
            ],
            [
                'title' => 'Promo Spesial Hari Ini',
                'subtitle' => 'Diskon Hingga 70%',
                'description' => 'Jangan lewatkan kesempatan emas! Promo spesial dengan diskon fantastis untuk berbagai kategori produk pilihan. Hanya hari ini!',
                'image' => 'banners/placeholder.jpg',
                'button_text' => 'Lihat Promo',
                'is_active' => true,
                'sort_order' => 3
            ],
            [
                'title' => 'Kesehatan & Kecantikan',
                'subtitle' => 'Rawat Diri Anda',
                'description' => 'Produk kesehatan dan kecantikan terpercaya untuk merawat tubuh dan wajah Anda. Skincare, makeup, dan suplemen kesehatan berkualitas.',
                'image' => 'banners/placeholder.jpg',
                'button_text' => 'Mulai Merawat',
                'is_active' => true,
                'sort_order' => 4
            ],
            [
                'title' => 'Perabotan Rumah Tangga',
                'subtitle' => 'Rumah Impian Anda',
                'description' => 'Lengkapi rumah dengan furniture dan dekorasi berkualitas. Sofa, meja, lemari, dan aksesoris rumah dengan desain modern dan elegan.',
                'image' => 'banners/placeholder.jpg',
                'button_text' => 'Dekorasi Rumah',
                'is_active' => true,
                'sort_order' => 5
            ],
            [
                'title' => 'Olahraga & Outdoor',
                'subtitle' => 'Hidup Sehat & Aktif',
                'description' => 'Peralatan olahraga dan outdoor untuk gaya hidup aktif Anda. Sepatu olahraga, pakaian gym, dan equipment fitness berkualitas.',
                'image' => 'banners/placeholder.jpg',
                'button_text' => 'Start Workout',
                'is_active' => true,
                'sort_order' => 6
            ],
            [
                'title' => 'Mainan & Hobi',
                'subtitle' => 'Hiburan untuk Keluarga',
                'description' => 'Koleksi mainan edukatif dan hobi untuk segala usia. Board games, puzzle, action figure, dan mainan kreatif untuk mengisi waktu luang.',
                'image' => 'banners/placeholder.jpg',
                'button_text' => 'Bermain Yuk',
                'is_active' => false, // Inactive banner for testing
                'sort_order' => 7
            ],
            [
                'title' => 'Gratis Ongkir Seluruh Indonesia',
                'subtitle' => 'Hemat Lebih Banyak',
                'description' => 'Nikmati pengiriman gratis ke seluruh nusantara untuk pembelian minimal Rp 100.000. Belanja lebih hemat, untung lebih banyak!',
                'image' => 'banners/placeholder.jpg',
                'button_text' => 'Belanja Gratis Ongkir',
                'is_active' => true,
                'sort_order' => 8
            ]
        ];

        foreach ($banners as $banner) {
            Banner::create($banner);
        }
    }
}