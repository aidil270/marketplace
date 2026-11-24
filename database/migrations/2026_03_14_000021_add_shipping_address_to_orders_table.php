<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->text('shipping_address')->nullable()->after('status');
        });

        // Salin snapshot alamat pengiriman dari shipments jika order lama sudah memiliki shipment
        try {
            DB::statement("
                UPDATE orders o
                INNER JOIN shipments s ON s.order_id = o.id
                SET o.shipping_address = s.recipient_address
                WHERE o.shipping_address IS NULL
            ");
        } catch (\Throwable $e) {
            // Abaikan jika database masih kosong
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('shipping_address');
        });
    }
};
