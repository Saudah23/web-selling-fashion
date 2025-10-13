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
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('filename'); // Original filename
            $table->string('path'); // Storage path
            $table->string('url'); // Public URL
            $table->string('alt_text')->nullable(); // Alt text for accessibility
            $table->boolean('is_primary')->default(false); // Main/default image
            $table->integer('sort_order')->default(0); // Display order
            $table->bigInteger('file_size')->nullable(); // File size in bytes
            $table->string('mime_type')->nullable(); // MIME type
            $table->json('metadata')->nullable(); // Additional metadata (dimensions, etc.)
            $table->timestamps();

            // Indexes for performance
            $table->index(['product_id', 'is_primary']);
            $table->index(['product_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
