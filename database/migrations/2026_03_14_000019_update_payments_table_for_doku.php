<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('gateway')->default('doku')->after('order_id');
            $table->string('payment_method')->nullable()->change();
            $table->string('reference_number')->nullable()->unique()->after('payment_method');
            $table->text('payment_url')->nullable()->after('reference_number');
            $table->decimal('amount', 15, 2)->change();
            $table->dropIndex(['payment_status']);
            $table->renameColumn('payment_status', 'status');
            $table->timestamp('expired_at')->nullable()->after('paid_at');
            $table->json('gateway_response')->nullable()->after('expired_at');

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropUnique(['reference_number']);
            $table->renameColumn('status', 'payment_status');
            $table->index('payment_status');
            $table->decimal('amount', 12, 2)->change();
            $table->string('payment_method')->nullable(false)->change();
            $table->dropColumn(['gateway', 'reference_number', 'payment_url', 'expired_at', 'gateway_response']);
        });
    }
};
