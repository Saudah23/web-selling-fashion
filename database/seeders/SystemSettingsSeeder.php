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
            // General Settings
            [
                'key' => 'app_name',
                'value' => 'Fashion Marketplace',
                'type' => 'text',
                'group' => 'general',
                'description' => 'Application name',
                'is_public' => true,
            ],
            [
                'key' => 'app_description',
                'value' => 'Premium fashion marketplace for modern clothing',
                'type' => 'text',
                'group' => 'general',
                'description' => 'Application description',
                'is_public' => true,
            ],
            [
                'key' => 'contact_email',
                'value' => 'support@fashionstore.com',
                'type' => 'text',
                'group' => 'general',
                'description' => 'Contact email address',
                'is_public' => true,
            ],
            [
                'key' => 'contact_phone',
                'value' => '+62 812-3456-7890',
                'type' => 'text',
                'group' => 'general',
                'description' => 'Contact phone number',
                'is_public' => true,
            ],
            [
                'key' => 'business_hours',
                'value' => "Monday - Friday: 09:00 - 18:00\nSaturday: 09:00 - 15:00\nSunday: Closed",
                'type' => 'text',
                'group' => 'general',
                'description' => 'Business operating hours',
                'is_public' => true,
            ],

            // Shipping Settings (RajaOngkir)
            [
                'key' => 'shipping_origin_province_id',
                'value' => '6', // DKI Jakarta (example)
                'type' => 'number',
                'group' => 'shipping',
                'description' => 'Shipping origin province ID (wilayah.id)',
                'is_public' => false,
            ],
            [
                'key' => 'shipping_origin_city_id',
                'value' => '151', // Jakarta Pusat (example)
                'type' => 'number',
                'group' => 'shipping',
                'description' => 'Shipping origin city ID (wilayah.id)',
                'is_public' => false,
            ],
            [
                'key' => 'shipping_origin_district_id',
                'value' => '1871', // Menteng (example)
                'type' => 'number',
                'group' => 'shipping',
                'description' => 'Shipping origin district ID (wilayah.id)',
                'is_public' => false,
            ],
            [
                'key' => 'shipping_origin_address',
                'value' => 'Jl. MH Thamrin No. 10, Jakarta Pusat',
                'type' => 'text',
                'group' => 'shipping',
                'description' => 'Complete shipping origin address',
                'is_public' => false,
            ],
            [
                'key' => 'rajaongkir_api_key',
                'value' => env('RAJAONGKIR_API_KEY', '8c8add072dfe923147fdfdbf3a8fd448'), // Auto-fill from env or default
                'type' => 'text',
                'group' => 'shipping',
                'description' => 'RajaOngkir API Key',
                'is_public' => false,
            ],
            [
                'key' => 'rajaongkir_origin_city_id',
                'value' => '151', // Jakarta Pusat RajaOngkir ID
                'type' => 'number',
                'group' => 'shipping',
                'description' => 'RajaOngkir origin city ID for shipping calculation',
                'is_public' => false,
            ],
            [
                'key' => 'supported_couriers',
                'value' => json_encode(['jne', 'jnt']),
                'type' => 'json',
                'group' => 'shipping',
                'description' => 'Supported shipping couriers',
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

        $this->command->info('✅ System settings seeded successfully!');
        $this->command->info('📍 Default shipping origin: Jakarta Pusat');
        $this->command->info('💳 Payment gateways: Midtrans (needs configuration)');
        $this->command->info('🚚 Shipping: RajaOngkir (needs API key)');
    }
}