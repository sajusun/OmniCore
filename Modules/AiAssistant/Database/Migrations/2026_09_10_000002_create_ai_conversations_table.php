<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_conversations', function (Blueprint $table) {
            $table->id();
            
            $userModel = config('auth.providers.users.model', 'App\\Models\\User');
            $table->foreignIdFor($userModel, 'user_id')->constrained()->cascadeOnDelete();

            $table->foreignId('persona_id')->nullable()->constrained('ai_personas')->nullOnDelete();
            $table->string('title')->default('New Code Session');
            $table->string('model_used')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'updated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_conversations');
    }
};
