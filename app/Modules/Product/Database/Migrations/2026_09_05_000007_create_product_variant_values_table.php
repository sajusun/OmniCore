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
        Schema::create('product_variant_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->foreignId('attribute_id')->constrained('product_attributes')->cascadeOnDelete();
            $table->foreignId('attribute_value_id')->constrained('product_attribute_values')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['variant_id', 'attribute_id', 'attribute_value_id'], 'var_attr_val_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variant_values');
    }
};
