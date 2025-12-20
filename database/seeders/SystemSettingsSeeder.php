<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings - FASHION SAAZZ
            [
                'key' => 'app_name',
                'value' => 'FASHION SAAZZ',
                'type' => 'text',
                'group' => 'general',
                'description' => 'Nama aplikasi',
                'is_public' => true,
            ],
            [
                'key' => 'app_description',
                'value' => 'Toko fashion online terpercaya untuk gaya Anda',
                'type' => 'text',
                'group' => 'general',
                'description' => 'Deskripsi aplikasi',
                'is_public' => true,
            ],
            [
                'key' => 'contact_email',
                'value' => 'FashionSaazzz@gmail.com',
                'type' => 'text',
                'group' => 'general',
                'description' => 'Email kontak',
                'is_public' => true,
            ],
            [
                'key' => 'contact_phone',
                'value' => '+6287827683335',
                'type' => 'text',
                'group' => 'general',
                'description' => 'Nomor telepon kontak',
                'is_public' => true,
            ],
            [
                'key' => 'contact_address',
                'value' => 'Komplek Pesona Pondok Indah, RT 12/RW 06, Kec. Bati-Bati, Kab. Tanah Laut, Prov. Kalimantan Selatan',
                'type' => 'text',
                'group' => 'general',
                'description' => 'Alamat toko',
                'is_public' => true,
            ],
            [
                'key' => 'contact_instagram',
                'value' => '@Saaazz.id',
                'type' => 'text',
                'group' => 'general',
                'description' => 'Akun Instagram',
                'is_public' => true,
            ],
            [
                'key' => 'business_hours',
                'value' => "Senin - Jumat: 09:00 - 18:00\nSabtu: 09:00 - 15:00\nMinggu: Tutup",
                'type' => 'text',
                'group' => 'general',
                'description' => 'Jam operasional',
                'is_public' => true,
            ],

            // Shipping Settings (RajaOngkir) - Lokasi Tanah Laut
            [
                'key' => 'shipping_origin_province_id',
                'value' => '22', // Kalimantan Selatan
                'type' => 'number',
                'group' => 'shipping',
                'description' => 'ID provinsi asal pengiriman',
                'is_public' => false,
            ],
            [
                'key' => 'shipping_origin_city_id',
                'value' => '343', // Kabupaten Tanah Laut
                'type' => 'number',
                'group' => 'shipping',
                'description' => 'ID kota asal pengiriman',
                'is_public' => false,
            ],
            [
                'key' => 'shipping_origin_district_id',
                'value' => '4905', // Bati-Bati
                'type' => 'number',
                'group' => 'shipping',
                'description' => 'ID kecamatan asal pengiriman',
                'is_public' => false,
            ],
            [
                'key' => 'shipping_origin_address',
                'value' => 'Komplek Pesona Pondok Indah, RT 12/RW 06, Kec. Bati-Bati',
                'type' => 'text',
                'group' => 'shipping',
                'description' => 'Alamat lengkap asal pengiriman',
                'is_public' => false,
            ],
            [
                'key' => 'rajaongkir_api_key',
                'value' => env('RAJAONGKIR_API_KEY', '8c8add072dfe923147fdfdbf3a8fd448'),
                'type' => 'text',
                'group' => 'shipping',
                'description' => 'RajaOngkir API Key',
                'is_public' => false,
            ],
            [
                'key' => 'rajaongkir_origin_city_id',
                'value' => '42', // Tanah Laut RajaOngkir ID
                'type' => 'number',
                'group' => 'shipping',
                'description' => 'ID kota RajaOngkir untuk kalkulasi ongkir',
                'is_public' => false,
            ],
            [
                'key' => 'supported_couriers',
                'value' => json_encode(['jne', 'jnt']),
                'type' => 'json',
                'group' => 'shipping',
                'description' => 'Kurir yang didukung (JNE & J&T)',
                'is_public' => true,
            ],

            // Payment Settings (Midtrans)
            [
                'key' => 'midtrans_server_key',
                'value' => env('MIDTRANS_SERVER_KEY', ''), // Auto-fill from env
                'type' => 'text',
                'group' => 'payment',
                'description' => 'Midtrans Server Key',
                'is_public' => false,
            ],
            [
                'key' => 'midtrans_client_key',
                'value' => env('MIDTRANS_CLIENT_KEY', ''), // Auto-fill from env
                'type' => 'text',
                'group' => 'payment',
                'description' => 'Midtrans Client Key',
                'is_public' => false,
            ],
            [
                'key' => 'midtrans_merchant_id',
                'value' => env('MIDTRANS_MERCHANT_ID', ''), // Auto-fill from env
                'type' => 'text',
                'group' => 'payment',
                'description' => 'Midtrans Merchant ID',
                'is_public' => false,
            ],
            [
                'key' => 'midtrans_environment',
                'value' => env('MIDTRANS_ENVIRONMENT', 'sandbox'), // Auto-fill from env or default to sandbox
                'type' => 'text',
                'group' => 'payment',
                'description' => 'Midtrans Environment (sandbox/production)',
                'is_public' => false,
            ],
            [
                'key' => 'payment_methods',
                'value' => json_encode([
                    'credit_card',
                    'bca_va',
                    'bni_va',
                    'bri_va',
                    'mandiri_va',
                    'gopay',
                    'shopeepay',
                    'qris'
                ]),
                'type' => 'json',
                'group' => 'payment',
                'description' => 'Enabled payment methods',
                'is_public' => true,
            ],

        ];

        foreach ($settings as $setting) {
            $setting['created_at'] = now();
            $setting['updated_at'] = now();
        }

        DB::table('system_settings')->insert($settings);

        $this->command->info('✅ Pengaturan sistem berhasil disimpan!');
        $this->command->info('📍 Lokasi asal pengiriman: Tanah Laut, Kalimantan Selatan');
        $this->command->info('💳 Pembayaran: QRIS, GoPay, ShopeePay via Midtrans');
        $this->command->info('🚚 Kurir: JNE & J&T via RajaOngkir');
    }
}