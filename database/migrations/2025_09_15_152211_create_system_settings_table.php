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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->index(); // Setting identifier
            $table->text('value')->nullable(); // Setting value (JSON for complex data)
            $table->string('type')->default('text'); // text, json, boolean, number
            $table->string('group')->default('general'); // general, shipping, payment, etc.
            $table->string('description')->nullable();
            $table->boolean('is_public')->default(false); // Can be accessed by frontend
            $table->timestamps();

            $table->index(['group', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};