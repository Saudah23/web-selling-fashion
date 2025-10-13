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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();

            // Order Reference
            $table->string('order_id')->unique()->index(); // Internal order ID
            $table->foreignId('user_id')->constrained('users');

            // Transaction Details
            $table->string('transaction_id')->unique()->nullable(); // Midtrans transaction ID
            $table->string('payment_type')->nullable(); // credit_card, bank_transfer, etc.
            $table->decimal('gross_amount', 15, 2); // Total amount
            $table->string('currency', 3)->default('IDR');

            // Transaction Status
            $table->enum('status', [
                'pending',      // Waiting for payment
                'settlement',   // Payment success
                'capture',      // Credit card captured
                'deny',         // Payment denied
                'cancel',       // Transaction cancelled
                'expire',       // Transaction expired
                'failure',      // Payment failed
                'refund',       // Refunded
                'partial_refund' // Partially refunded
            ])->default('pending');

            // Midtrans Response Data
            $table->string('fraud_status')->nullable(); // accept, challenge, deny
            $table->timestamp('transaction_time')->nullable();
            $table->timestamp('settlement_time')->nullable();
            $table->json('midtrans_response')->nullable(); // Full response from Midtrans

            // Customer Details
            $table->json('customer_details')->nullable(); // Customer info sent to Midtrans
            $table->json('item_details')->nullable(); // Items sent to Midtrans

            // Shipping Information
            $table->json('shipping_address')->nullable(); // Shipping address
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->string('shipping_service')->nullable(); // JNE, TIKI, etc.

            // Payment URLs (from Midtrans)
            $table->text('payment_url')->nullable(); // Redirect URL for payment
            $table->text('pdf_url')->nullable(); // PDF receipt URL

            // Additional Info
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // Additional data

            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'status']);
            $table->index(['order_id', 'status']);
            $table->index(['transaction_id', 'status']);
            $table->index('transaction_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};