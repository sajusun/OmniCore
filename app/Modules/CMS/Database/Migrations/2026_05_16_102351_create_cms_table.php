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
        if (!Schema::hasTable('cms')) {
            Schema::create('cms', function (Blueprint $table) {
                $table->id();
                $table->string('page')->comment('Page Name');
                $table->string('section')->comment('Section Name');
                $table->string('name')->nullable()->comment('Title for section in only admin dashboard to understand easily');
                $table->string('title')->nullable()->comment('Section Title');
                $table->string('subtitle')->nullable()->comment('Section Sub Title');
                $table->longText('description')->nullable()->comment('Section Description');
                $table->text('short_description')->nullable()->comment('Section Short Description');
                $table->string('image')->nullable()->comment('Section Image (Single)');
                $table->string('bg')->nullable()->comment('Section Background Image (Single)');
                $table->string('video')->nullable()->comment('Section Video Link');
                $table->json('meta')->nullable()->comment('Custom key-value builder for extra dynamic section elements');
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms');
    }
};
