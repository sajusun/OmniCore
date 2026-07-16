<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_user', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            // optional metadata (useful for scanner apps)
            $table->string('source')->nullable(); // scan / search / manual
            $table->timestamp('added_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'product_id'], 'user_product_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_user');
    }
};
