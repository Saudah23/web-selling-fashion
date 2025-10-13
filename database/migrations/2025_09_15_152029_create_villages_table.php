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
        Schema::create('villages', function (Blueprint $table) {
            $table->id();
            $table->string('wilayah_id')->unique()->index(); // ID from wilayah.id
            $table->string('name');
            $table->foreignId('district_id')->constrained('districts')->cascadeOnDelete();
            $table->string('postal_code')->nullable()->index(); // For shipping calculations
            $table->timestamps();

            $table->index(['wilayah_id', 'district_id', 'postal_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('villages');
    }
};