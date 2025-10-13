<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Owner
        User::create([
            'name' => 'Fashion Store Owner',
            'email' => 'owner@fashionstore.com',
            'password' => Hash::make('password123'),
            'role' => 'owner',
            'email_verified_at' => now(),
        ]);

        // Create Admin
        User::create([
            'name' => 'Store Administrator',
            'email' => 'admin@fashionstore.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create 10 Customers with proper fashion marketplace names
        $customers = [
            ['name' => 'Sarah Johnson', 'email' => 'sarah.johnson@example.com'],
            ['name' => 'Michael Chen', 'email' => 'michael.chen@example.com'],
            ['name' => 'Emily Rodriguez', 'email' => 'emily.rodriguez@example.com'],
            ['name' => 'David Thompson', 'email' => 'david.thompson@example.com'],
            ['name' => 'Jessica Kim', 'email' => 'jessica.kim@example.com'],
            ['name' => 'Alex Wilson', 'email' => 'alex.wilson@example.com'],
            ['name' => 'Maria Garcia', 'email' => 'maria.garcia@example.com'],
            ['name' => 'James Miller', 'email' => 'james.miller@example.com'],
            ['name' => 'Anna Lee', 'email' => 'anna.lee@example.com'],
            ['name' => 'Robert Brown', 'email' => 'robert.brown@example.com'],
        ];

        foreach ($customers as $customer) {
            User::create([
                'name' => $customer['name'],
                'email' => $customer['email'],
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'email_verified_at' => now(),
            ]);
        }

        $this->command->info('✅ Users seeded successfully!');
        $this->command->info('📧 Owner: owner@fashionstore.com | Password: password123');
        $this->command->info('📧 Admin: admin@fashionstore.com | Password: password123');
        $this->command->info('📧 Customers: Various emails | Password: password123');
    }
}