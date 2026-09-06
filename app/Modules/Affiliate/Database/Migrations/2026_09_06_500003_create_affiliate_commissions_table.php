<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained('affiliate_accounts')->cascadeOnDelete();
            $table->foreignId('referral_id')->nullable()->constrained('affiliate_referrals')->nullOnDelete();
            $table->nullableMorphs('reference'); // Order, Subscription, etc.
            $table->decimal('order_amount', 15, 2)->default(0.00);
            $table->decimal('commission_rate', 8, 2)->default(10.00);
            $table->decimal('commission_amount', 15, 2)->default(0.00);
            $table->string('status')->default('pending'); // pending, approved, paid, rejected
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['affiliate_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_commissions');
    }
};
