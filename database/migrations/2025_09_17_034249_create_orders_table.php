<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Order totals
            $table->decimal('subtotal', 15, 2);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 15, 2);

            // Order status
            $table->enum('status', [
                'pending',      // Order created, waiting for payment
                'paid',         // Payment received
                'processing',   // Order being prepared
                'shipped',      // Order shipped
                'delivered',    // Order delivered
                'cancelled',    // Order cancelled
                'refunded'      // Order refunded
            ])->default('pending');

            // Shipping information
            $table->json('shipping_address'); // Customer shipping address
            $table->string('shipping_service')->nullable(); // JNE REG, TIKI ONS, etc.
            $table->string('shipping_courier')->nullable(); // jne, tiki, pos
            $table->string('shipping_etd')->nullable(); // Estimated delivery time
            $table->string('tracking_number')->nullable();

            // Payment information
            $table->string('payment_method')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();

            // Additional info
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // Additional order data

            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['status', 'created_at']);
            $table->index('order_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};