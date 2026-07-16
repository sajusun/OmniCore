<?php

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
        Schema::create('media', function (Blueprint $table) {

            $table->id();
            $table->morphs('mediable');
            $table->string('disk')->default('public');

            $table->string('collection_name')->nullable();
            $table->string('original_name')->nullable();
            $table->string('file_name')->nullable();

            $table->string('mime_type')->nullable();
            $table->string('extension', 20)->nullable();
            $table->unsignedBigInteger('size')->default(0);

            $table->string('path')->nullable();
            $table->string('url')->nullable();

            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->json('meta')->nullable();

            $table->integer('sort_order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->string('status')->default('active')->comment('active, inactive, archived');
            
            $table->timestamps();

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};