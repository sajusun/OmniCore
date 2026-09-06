<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->uuid('payment_id')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->nullableMorphs('payable'); // Polymorphic (Order, Subscription, WalletDeposit)
            $table->string('gateway', 30); // stripe, paypal, sslcommerz, bkash, wallet, cod, bank_transfer
            $table->decimal('amount', 16, 2);
            $table->decimal('fee', 16, 2)->default(0.00);
            $table->string('currency', 10)->default('USD');
            $table->string('status', 30)->default('pending'); // pending, processing, completed, failed, refunded, canceled
            $table->string('gateway_transaction_id')->nullable();
            $table->string('gateway_reference')->nullable();
            $table->text('checkout_url')->nullable();
            $table->text('return_url')->nullable();
            $table->text('cancel_url')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->json('gateway_response')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['gateway', 'status']);
            $table->index('gateway_transaction_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
