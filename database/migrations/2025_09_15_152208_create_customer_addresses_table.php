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
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Address Label & Status
            $table->string('label')->default('Home'); // Home, Office, Other
            $table->boolean('is_default')->default(false);

            // Recipient Information
            $table->string('recipient_name');
            $table->string('recipient_phone');

            // Address Components (Wilayah.id references)
            $table->foreignId('province_id')->constrained('provinces');
            $table->foreignId('city_id')->constrained('cities');
            $table->foreignId('district_id')->constrained('districts');
            $table->foreignId('village_id')->constrained('villages');

            // Detailed Address
            $table->text('address_detail'); // Street, number, etc.
            $table->string('postal_code', 10);
            $table->text('notes')->nullable(); // Additional delivery notes

            // Coordinates (optional for precise delivery)
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'is_default']);
            $table->index(['province_id', 'city_id']);
            $table->index('postal_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_addresses');
    }
};