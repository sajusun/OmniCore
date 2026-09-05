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
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('likeable'); // likeable_type, likeable_id
            $table->string('type', 32)->default('like'); // 'like', 'love', 'fire', 'clap', etc.
            $table->timestamps();

            $table->unique(['user_id', 'likeable_type', 'likeable_id'], 'unique_user_likeable');
            $table->index(['likeable_type', 'likeable_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
};
