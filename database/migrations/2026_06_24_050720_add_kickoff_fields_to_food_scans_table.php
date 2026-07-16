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
            $table->boolean('ojais_approved')->default(false)->after('verdict_label');
            $table->integer('metabolic_score')->nullable()->after('ojais_approved');
            $table->string('metabolic_verdict')->nullable()->after('metabolic_score');
            $table->json('harmful_ingredients')->nullable()->after('metabolic_verdict');
            $table->json('penalty_flags')->nullable()->after('harmful_ingredients');
            $table->json('score_breakdown')->nullable()->after('penalty_flags');
            $table->boolean('uncertainty_flag')->default(false)->after('score_breakdown');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('food_scans', function (Blueprint $table) {
            $table->dropColumn([
                'ojais_approved',
                'metabolic_score',
                'metabolic_verdict',
                'harmful_ingredients',
                'penalty_flags',
                'score_breakdown',
                'uncertainty_flag',
            ]);
        });
    }
};
