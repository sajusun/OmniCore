<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('clubs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['car', 'motorcycle']);
            $table->string('country');
            $table->string('state');
            $table->string('city');
            $table->text('description')->nullable();
            
            // // Club branding fields
            // $table->string('cover_image')->nullable();
            // $table->json('photos')->nullable(); 
            // $table->json('videos')->nullable(); 

            $table->enum('status', ['draft', 'published', 'pending'])->default('draft');
            
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('clubs');
    }
};