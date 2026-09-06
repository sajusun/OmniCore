<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('point_transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->integer('amount'); // Positive for credit, negative for debit
            $table->string('type', 30); // earned, spent, redeemed, expired, adjusted
            $table->integer('before_balance');
            $table->integer('after_balance');
            $table->string('description')->nullable();
            $table->nullableMorphs('reference'); // e.g. Order, DailyCheckin, Referral, Badge
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_transactions');
    }
};
