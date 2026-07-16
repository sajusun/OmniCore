<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nutrition_goals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            $table->string("goal")->nullable();
            $table->string('diet_style')->nullable();
            $table->integer('daily_calories')->nullable();
            $table->integer('daily_protein')->nullable(); // grams
            $table->integer('daily_carbs')->nullable(); // grams
            $table->integer('weekly_sugar_limit')->nullable(); // grams
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('nutrition_goals');
    }
};
