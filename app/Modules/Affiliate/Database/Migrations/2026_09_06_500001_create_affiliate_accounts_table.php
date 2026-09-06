<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('referral_code')->unique();
            $table->string('custom_slug')->nullable()->unique();
            $table->decimal('commission_rate', 8, 2)->default(10.00); // 10%
            $table->string('commission_type')->default('percentage'); // percentage, fixed
            $table->string('status')->default('active'); // pending, active, suspended, rejected
            $table->decimal('total_earnings', 15, 2)->default(0.00);
            $table->decimal('paid_earnings', 15, 2)->default(0.00);
            $table->decimal('current_balance', 15, 2)->default(0.00);
            $table->unsignedInteger('lifetime_referrals')->default(0);
            $table->unsignedInteger('lifetime_conversions')->default(0);
            $table->json('payout_settings')->nullable();
            $table->timestamps();

            $table->index(['status', 'referral_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_accounts');
    }
};
