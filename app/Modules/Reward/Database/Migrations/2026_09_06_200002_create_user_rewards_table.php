<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->integer('points_balance')->default(0);
            $table->integer('lifetime_points')->default(0);
            $table->foreignId('tier_id')->nullable()->constrained('reward_tiers')->nullOnDelete();
            $table->integer('streak_days')->default(0);
            $table->timestamp('last_checkin_at')->nullable();
            $table->timestamps();

            $table->index('points_balance');
            $table->index('lifetime_points');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_rewards');
    }
};
