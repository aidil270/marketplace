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
        // 1. Tambah kolom phone pada tabel stores
        Schema::table('stores', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('city');
        });

        // 2. Tambah softDeletes pada tabel products agar produk yang pernah dipesan tidak hilang riwayatnya
        Schema::table('products', function (Blueprint $table) {
            $table->softDeletes()->after('is_featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('phone');
        });
    }
};
