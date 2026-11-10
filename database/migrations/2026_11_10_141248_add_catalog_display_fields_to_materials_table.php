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
        Schema::table('materials', function (Blueprint $table) {
            $table->string('spec_badge')->nullable()->after('sale_unit');
            $table->string('unit_detail')->nullable()->after('spec_badge');
            $table->string('sub_price_badge')->nullable()->after('unit_detail');
            $table->string('stock_badge')->default('Tersedia')->after('availability_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn(['spec_badge', 'unit_detail', 'sub_price_badge', 'stock_badge']);
        });
    }
};
