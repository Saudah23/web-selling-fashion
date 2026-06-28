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
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'compare_price')) {
                $table->dropColumn('compare_price');
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'product_compare_price')) {
                $table->dropColumn('product_compare_price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('compare_price', 15, 2)->nullable()->after('price');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('product_compare_price', 15, 2)->nullable()->after('product_price');
        });
    }
};
