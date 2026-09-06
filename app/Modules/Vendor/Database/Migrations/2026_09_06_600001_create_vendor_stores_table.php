<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_stores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('banner_path')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->decimal('commission_rate', 8, 2)->default(10.00); // 10% platform fee
            $table->string('status')->default('pending'); // pending, active, suspended, rejected
            $table->decimal('total_sales', 15, 2)->default(0.00);
            $table->decimal('total_earnings', 15, 2)->default(0.00); // After platform commission
            $table->decimal('balance', 15, 2)->default(0.00); // Withdrawable balance
            $table->boolean('is_featured')->default(false);
            $table->json('payout_settings')->nullable(); // Bank or Wallet details
            $table->json('business_documents')->nullable();
            $table->timestamps();

            $table->index(['status', 'is_featured']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_stores');
    }
};
