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
        Schema::create('food_scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('image_url')->nullable();
            $table->string('product_name')->nullable();
            $table->json('identified_foods')->nullable();
            $table->integer('ojais_score')->nullable();
            $table->string('verdict_key')->nullable();
            $table->string('verdict_label')->nullable();
            $table->json('nutrition')->nullable();
            $table->text('insight')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_scans');
    }
};
