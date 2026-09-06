<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_knowledge_bases', function (Blueprint $table) {
            $table->id();
            $table->string('category')->default('general');
            $table->string('question');
            $table->longText('answer');
            $table->json('keywords')->nullable();
            $table->json('tags')->nullable();
            $table->unsignedInteger('hit_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['category', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_knowledge_bases');
    }
};
