<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('wallet_id')->constrained('wallets')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type', 30); // deposit, withdrawal, payment, refund, transfer_in, transfer_out, fee, adjustment
            $table->decimal('amount', 16, 2);
            $table->decimal('fee', 16, 2)->default(0.00);
            $table->decimal('before_balance', 16, 2);
            $table->decimal('after_balance', 16, 2);
            $table->string('currency', 10)->default('USD');
            $table->string('description')->nullable();
            $table->nullableMorphs('reference'); // Polymorphic link (e.g. Order, Payment, WithdrawalRequest, UserTransfer)
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'type']);
            $table->index(['wallet_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
