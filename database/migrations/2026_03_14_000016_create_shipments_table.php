<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('courier_name'); // JNE, J&T, SiCepat
            $table->string('service_type')->default('REG');
            $table->string('tracking_number')->nullable();
            $table->string('shipment_status')->default('pending'); // pending, shipped, delivered, returned
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('recipient_address');
            $table->timestamps();

            $table->index('order_id');
            $table->index('tracking_number');
            $table->index('shipment_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
