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
        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->string('wilayah_id')->unique()->index(); // ID from wilayah.id
            $table->string('name');
            $table->string('rajaongkir_id')->nullable()->index(); // RajaOngkir province ID
            $table->timestamps();

            $table->index(['wilayah_id', 'rajaongkir_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provinces');
    }
};