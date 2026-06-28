<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $statuses = [
        'pending', 'paid', 'processing', 'shipped',
        'delivered', 'cancelled', 'refunded', 'expired',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // SQLite menyimpan enum sebagai varchar + CHECK constraint yang
            // membatasi nilai. Ubah ke string biasa agar 'expired' diterima.
            Schema::table('orders', function (Blueprint $table) {
                $table->string('status')->default('pending')->change();
            });
        } else {
            $values = "'" . implode("','", $this->statuses) . "'";
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM($values) NOT NULL DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan order berstatus expired ke cancelled agar tidak melanggar enum
        DB::table('orders')->where('status', 'expired')->update(['status' => 'cancelled']);

        $original = "'pending','paid','processing','shipped','delivered','cancelled','refunded'";

        if (DB::getDriverName() === 'sqlite') {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('status')->default('pending')->change();
            });
        } else {
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM($original) NOT NULL DEFAULT 'pending'");
        }
    }
};
