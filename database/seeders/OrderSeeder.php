<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role', 'customer')->get();
        $products = Product::where('is_active', true)->get();

        if ($customers->isEmpty() || $products->isEmpty()) {
            $this->command->warn('No customers or products found. Run UserSeeder and ProductSeeder first.');
            return;
        }

        $statuses = ['pending', 'paid', 'processing', 'shipped', 'delivered', 'delivered', 'delivered'];
        $couriers = ['jne', 'jnt', 'sicepat', 'pos'];
        $services = ['REG', 'YES', 'OKE'];
        $paymentMethods = ['bank_transfer', 'midtrans'];

        $addresses = [
            ['name' => 'Sarah Johnson', 'phone' => '081234567890', 'address' => 'Jl. Sudirman No. 10', 'city' => 'Jakarta Pusat', 'province' => 'DKI Jakarta', 'postal_code' => '10220'],
            ['name' => 'Michael Chen', 'phone' => '082345678901', 'address' => 'Jl. Gatot Subroto No. 5', 'city' => 'Bandung', 'province' => 'Jawa Barat', 'postal_code' => '40261'],
            ['name' => 'Emily Rodriguez', 'phone' => '083456789012', 'address' => 'Jl. Pemuda No. 22', 'city' => 'Surabaya', 'province' => 'Jawa Timur', 'postal_code' => '60271'],
            ['name' => 'David Thompson', 'phone' => '084567890123', 'address' => 'Jl. Diponegoro No. 8', 'city' => 'Yogyakarta', 'province' => 'DI Yogyakarta', 'postal_code' => '55212'],
            ['name' => 'Jessica Kim', 'phone' => '085678901234', 'address' => 'Jl. Ahmad Yani No. 15', 'city' => 'Medan', 'province' => 'Sumatera Utara', 'postal_code' => '20112'],
        ];

        $orderCount = 0;
        $baseDate = now()->subDays(30);

        for ($i = 0; $i < 20; $i++) {
            $customer = $customers->random();
            $status = $statuses[array_rand($statuses)];
            $courier = $couriers[array_rand($couriers)];
            $service = $services[array_rand($services)];
            $address = $addresses[array_rand($addresses)];
            $paymentMethod = $paymentMethods[array_rand($paymentMethods)];

            // Pick 1-3 random products
            $selectedProducts = $products->random(rand(1, min(3, $products->count())));
            $subtotal = 0;
            $items = [];

            foreach ($selectedProducts as $product) {
                $qty = rand(1, 3);
                $itemSubtotal = $product->price * $qty;
                $subtotal += $itemSubtotal;
                $items[] = [
                    'product' => $product,
                    'qty' => $qty,
                    'subtotal' => $itemSubtotal,
                ];
            }

            $shippingCost = rand(1, 5) * 5000;
            $total = $subtotal + $shippingCost;

            $createdAt = $baseDate->copy()->addDays(rand(0, 30));

            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(substr(uniqid(), -8)),
                'user_id' => $customer->id,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'tax_amount' => 0,
                'total_amount' => $total,
                'status' => $status,
                'shipping_address' => $address,
                'shipping_service' => $courier . ' ' . $service,
                'shipping_courier' => $courier,
                'shipping_etd' => rand(2, 5) . '-' . rand(5, 7) . ' hari',
                'tracking_number' => in_array($status, ['shipped', 'delivered']) ? strtoupper($courier) . rand(100000000, 999999999) : null,
                'payment_method' => $paymentMethod,
                'paid_at' => in_array($status, ['paid', 'processing', 'shipped', 'delivered']) ? $createdAt->copy()->addHours(rand(1, 24)) : null,
                'shipped_at' => in_array($status, ['shipped', 'delivered']) ? $createdAt->copy()->addDays(rand(1, 3)) : null,
                'delivered_at' => $status === 'delivered' ? $createdAt->copy()->addDays(rand(4, 7)) : null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'product_name' => $item['product']->name,
                    'product_sku' => $item['product']->sku,
                    'product_price' => $item['product']->price,
                    'product_compare_price' => $item['product']->compare_price,
                    'product_image' => null,
                    'quantity' => $item['qty'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            $orderCount++;
        }

        $this->command->info("✅ Created {$orderCount} sample orders.");
    }
}
