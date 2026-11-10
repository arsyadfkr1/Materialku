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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();
            $table->string('idempotency_key')->unique();
            $table->string('tracking_token_hash')->unique();
            $table->string('customer_name');
            $table->string('whatsapp');
            $table->text('address');
            $table->text('customer_note')->nullable();
            $table->decimal('material_total', 12, 2);
            $table->string('status')->default('Menunggu Konfirmasi');
            $table->text('admin_note')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
