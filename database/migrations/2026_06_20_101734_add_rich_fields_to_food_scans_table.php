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
        Schema::table('food_scans', function (Blueprint $table) {
            $table->string('portion_estimation')->nullable()->after('verdict_label');
            $table->text('team_says_text')->nullable()->after('nutrition');
            $table->text('one_improvement')->nullable()->after('team_says_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('food_scans', function (Blueprint $table) {
            $table->dropColumn(['portion_estimation', 'team_says_text', 'one_improvement']);
        });
    }
};
