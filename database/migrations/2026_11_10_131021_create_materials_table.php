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
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('price', 12, 2);
            $table->string('sale_unit'); // dus, kaleng, sak, batang, lembar, dll.
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('availability_status')->default('available'); // available, out_of_stock

            // Parameter Kalkulator Material
            $table->boolean('calculator_enabled')->default(false);
            $table->string('calculation_type')->nullable(); // ceramic, paint
            $table->decimal('coverage_per_box', 8, 2)->nullable(); // m2 per dus (untuk keramik)
            $table->decimal('spread_per_liter_per_coat', 8, 2)->nullable(); // m2/liter/lapis (untuk cat)
            $table->decimal('package_volume_liters', 8, 2)->nullable(); // isi liter/kaleng (untuk cat)
            $table->unsignedInteger('default_coats')->nullable(); // jumlah lapis default
            $table->decimal('waste_ratio', 5, 2)->default(0.10); // misal 0.10 = 10%
            $table->text('calculation_note')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['category_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
