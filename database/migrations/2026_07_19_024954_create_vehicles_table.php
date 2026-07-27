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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('garage_id')->nullable()->constrained()->cascadeOnDelete();

            $table->string('name');
            $table->unsignedTinyInteger('brand');
            $table->string('model');
            $table->bigInteger('year')->nullable();

            $table->unsignedTinyInteger('vehicle_type');
            $table->unsignedTinyInteger('transmission');
            $table->unsignedTinyInteger('drive_type');

            $table->unsignedSmallInteger('horsepower')->nullable();
            $table->unsignedInteger('mileage')->nullable();

            $table->string('engine')->nullable();
            $table->string('color')->nullable();
            $table->string('vin')->nullable()->unique();
            
            $table->json('performance_mods')->nullable();
            $table->json('exterior_mods')->nullable();
            $table->json('suspension')->nullable();
            
            $table->text('build_story')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
