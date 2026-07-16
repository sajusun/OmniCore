<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms', function (Blueprint $table) {
            $table->id();
            $table->string('page');
            $table->string('section');
            $table->string('name')->nullable();
            $table->string('title')->nullable();
            $table->string('sub_title')->nullable();
            $table->longText('description')->nullable();
            $table->text('short_description')->nullable();
            $table->string('image')->nullable();
            $table->string('bg')->nullable();
            $table->string('video')->nullable();
            $table->json('meta')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();

            $table->unique(['page', 'section']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms');
    }
};