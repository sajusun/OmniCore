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
        Schema::table('nutrition_goals', function (Blueprint $table) {
            $table->integer('daily_fat')->nullable()->after('daily_carbs'); // grams
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nutrition_goals', function (Blueprint $table) {
            $table->dropColumn('daily_fat');
        });
    }
};
