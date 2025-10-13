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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');

            // Product details at time of order (snapshot)
            $table->string('product_name');
            $table->string('product_sku')->nullable();
            $table->decimal('product_price', 15, 2); // Price at time of purchase
            $table->decimal('product_compare_price', 15, 2)->nullable();
            $table->string('product_image')->nullable(); // Primary image at time of purchase

            // Order item details
            $table->integer('quantity');
            $table->decimal('subtotal', 15, 2); // quantity * product_price

            // Product attributes at time of purchase (color, size, etc.)
            $table->json('product_attributes')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['order_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};