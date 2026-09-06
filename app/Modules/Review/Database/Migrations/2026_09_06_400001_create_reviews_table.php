<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->morphs('reviewable'); // Product, Vendor, Post, etc.
            $table->unsignedTinyInteger('rating')->default(5); // 1 to 5
            $table->string('title')->nullable();
            $table->text('comment');
            $table->json('criteria_ratings')->nullable(); // e.g. {"quality": 5, "delivery": 4, "value": 5}
            $table->boolean('is_verified_buyer')->default(false);
            $table->string('status')->default('approved'); // approved, pending, rejected, flagged
            $table->unsignedInteger('helpful_count')->default(0);
            $table->unsignedInteger('unhelpful_count')->default(0);
            $table->text('vendor_reply')->nullable();
            $table->timestamp('vendor_replied_at')->nullable();
            $table->timestamps();

            $table->index(['reviewable_type', 'reviewable_id', 'status']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
