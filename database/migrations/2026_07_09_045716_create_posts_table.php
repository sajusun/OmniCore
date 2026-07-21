<?php

use App\Enums\PostStatusEnum;
use App\Enums\PostVisibilityEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('share_link')->unique();
            // Share Post Support
            $table->foreignId('shared_post_id')->nullable()->constrained('posts')->nullOnDelete();
            $table->string('title')->nullable();
            $table->string('slug')->nullable()->unique();
            $table->longText('content')->nullable();
            $table->string('thumbnail')->nullable();
            $table->enum('type', ['post', 'shared',])->default('post');
            $table->string('visibility')->default('public');
            $table->string('status')->default('published');

            $table->timestamp('published_at')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
