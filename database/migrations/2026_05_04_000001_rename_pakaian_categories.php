<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('categories')
            ->whereNull('parent_id')
            ->where('name', 'Pakaian')
            ->update(['name' => 'Pakaian Wanita', 'slug' => 'pakaian-wanita']);

        DB::table('categories')
            ->whereNull('parent_id')
            ->where('name', 'Pakaian Anak')
            ->update(['name' => 'Pakaian Anak-anak', 'slug' => 'pakaian-anak-anak']);
    }

    public function down(): void
    {
        DB::table('categories')
            ->whereNull('parent_id')
            ->where('name', 'Pakaian Wanita')
            ->update(['name' => 'Pakaian', 'slug' => 'pakaian']);

        DB::table('categories')
            ->whereNull('parent_id')
            ->where('name', 'Pakaian Anak-anak')
            ->update(['name' => 'Pakaian Anak', 'slug' => 'pakaian-anak']);
    }
};
