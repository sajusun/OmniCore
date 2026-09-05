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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('commentable'); // commentable_type, commentable_id
            $table->foreignId('parent_id')->nullable()->constrained('comments')->cascadeOnDelete();
            $table->text('body');
            $table->unsignedInteger('likes_count')->default(0);
            $table->unsignedInteger('replies_count')->default(0);
            $table->string('status')->default('approved'); // approved, pending, hidden, spam
            $table->timestamps();
            $table->softDeletes();

            $table->index(['commentable_type', 'commentable_id', 'created_at']);
            $table->index('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
