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
        // 1. Buat tabel payment_groups untuk menaungi satu atau banyak order dalam satu checkout
        Schema::create('payment_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('invoice_number')->unique();
            $table->decimal('total_amount', 15, 2);
            $table->string('status')->default('pending'); // pending, paid, failed, expired
            $table->string('reference_number')->nullable(); // ID/No Transaksi dari DOKU
            $table->text('payment_url')->nullable(); // URL hosted checkout DOKU
            $table->timestamp('expired_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('gateway_response')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('invoice_number');
            $table->index('status');
        });

        // 2. Tambahkan payment_group_id pada tabel orders
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('payment_group_id')->nullable()->after('store_id')->constrained('payment_groups')->nullOnDelete();
            $table->index('payment_group_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['payment_group_id']);
            $table->dropColumn('payment_group_id');
        });

        Schema::dropIfExists('payment_groups');
    }
};
