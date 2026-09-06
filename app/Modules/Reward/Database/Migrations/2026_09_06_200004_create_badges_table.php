<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('badge_type', 30)->default('achievement'); // achievement, streak, milestone, spending
            $table->integer('points_reward')->default(0); // Bonus points credited on unlock
            $table->string('criteria_type', 50)->nullable(); // e.g. orders_count, checkin_streak, spend_amount, referral_count
            $table->integer('criteria_threshold')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('criteria_type');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('badges');
    }
};
