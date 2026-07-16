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
        Schema::create('food_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('food_scan_id')->nullable()->constrained()->nullOnDelete();
            
            $table->string('product_name');
            $table->string('image')->nullable();
            $table->enum('meal_type', ['breakfast', 'lunch', 'dinner', 'snack'])->default('breakfast');
            $table->decimal('serving_size', 8, 2)->default(1);
            
            $table->integer('calories')->default(0);
            $table->integer('protein_g')->default(0);
            $table->integer('carbs_g')->default(0);
            $table->integer('fat_g')->default(0);
            $table->integer('food_score')->nullable();
            
            $table->date('logged_date');
            $table->time('logged_time');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_logs');
    }
};
