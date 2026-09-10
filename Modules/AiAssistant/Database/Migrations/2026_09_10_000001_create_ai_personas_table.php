<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_personas', function (Blueprint $table) {
            $table->id();
            
            // Decoupled user reference
            $userModel = config('auth.providers.users.model', 'App\\Models\\User');
            $table->foreignIdFor($userModel, 'user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->default('bot');
            $table->string('model')->default('qwen2.5-coder:7b');
            $table->text('system_prompt');
            $table->decimal('temperature', 3, 2)->default(0.70);
            $table->boolean('is_system_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_personas');
    }
};
