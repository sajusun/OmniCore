<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reward_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50); // Bronze, Silver, Gold, Platinum, Diamond
            $table->string('slug', 50)->unique();
            $table->text('description')->nullable();
            $table->integer('min_points')->default(0); // Minimum lifetime points to qualify
            $table->decimal('point_multiplier', 5, 2)->default(1.00); // e.g. 1.25x or 1.50x
            $table->decimal('discount_percent', 5, 2)->default(0.00); // Automatic store discount percent
            $table->json('perks')->nullable(); // Array of bullet perks
            $table->string('icon')->nullable();
            $table->string('color', 20)->default('#8fbd56');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('min_points');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reward_tiers');
    }
};
