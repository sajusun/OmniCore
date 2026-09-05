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
        Schema::create('bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('bookmarkable'); // bookmarkable_type, bookmarkable_id
            $table->string('collection', 64)->default('default'); // 'default', 'wishlist', 'favorite', 'saved'
            $table->timestamps();

            $table->unique(['user_id', 'bookmarkable_type', 'bookmarkable_id', 'collection'], 'unique_user_bookmark');
            $table->index(['bookmarkable_type', 'bookmarkable_id', 'collection']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookmarks');
    }
};
