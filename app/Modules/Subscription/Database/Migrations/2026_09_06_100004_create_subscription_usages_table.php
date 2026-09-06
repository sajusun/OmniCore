<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('subscriptions')->cascadeOnDelete();
            $table->string('feature_code', 50);
            $table->integer('used')->default(0);
            $table->timestamp('resets_at')->nullable();
            $table->timestamps();

            $table->unique(['subscription_id', 'feature_code']);
            $table->index(['subscription_id', 'feature_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_usages');
    }
};
