<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->string('upc')->unique();

            $table->string('name')->nullable();
            $table->string('brand')->nullable();

            $table->text('image_url')->nullable();
            $table->string('serving_size_text')->nullable();

            $table->decimal('calories', 10, 2)->nullable();
            $table->decimal('protein_g', 10, 2)->nullable();
            $table->decimal('carbs_g', 10, 2)->nullable();
            $table->decimal('fiber_g', 10, 2)->nullable();
            $table->decimal('sugar_g', 10, 2)->nullable();
            $table->decimal('sugar_alcohol_g', 10, 2)->nullable();
            $table->decimal('fat_g', 10, 2)->nullable();
            $table->decimal('sat_fat_g', 10, 2)->nullable();

            $table->longText('ingredients_text')->nullable();
            
            $table->string('source')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->enum('verdict', ['red','ojais_approved','neutral'])->default('neutral');
            $table->decimal('score', 5, 2)->nullable();
            $table->json('trigger_flags')->nullable();
            $table->json('explanations')->nullable();

            $table->decimal('seed_oil_score', 5, 2)->nullable();

            $table->timestamps();

            $table->index('brand');
            $table->index('verdict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
