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
        Schema::create('share_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->morphs('shareable'); // shareable_type, shareable_id
            $table->enum('type', ['public', 'private', 'single_use', 'expiring'])->default('public');
            $table->string('slug')->nullable()->index(); // SEO readable slug
            $table->string('token', 64)->nullable()->unique(); // Unique random token for private/expiring links
            $table->timestamp('expires_at')->nullable()->index();
            $table->unsignedInteger('max_clicks')->nullable(); // Max clicks allowed (e.g. 1 for single-use)
            $table->unsignedInteger('click_count')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->index(['shareable_type', 'shareable_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('share_links');
    }
};
