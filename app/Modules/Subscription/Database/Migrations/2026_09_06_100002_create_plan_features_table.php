<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('plans')->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('code', 50); // e.g. unlimited_chat, post_limit, priority_support, hd_streaming
            $table->string('value', 100)->default('true'); // e.g. "true", "unlimited", "50", "10"
            $table->boolean('is_limited')->default(false); // If true, value is treated as a numerical limit quota
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['plan_id', 'code']);
            $table->index('code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_features');
    }
};
