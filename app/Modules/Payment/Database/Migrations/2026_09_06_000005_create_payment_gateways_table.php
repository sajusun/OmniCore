<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50); // Stripe, PayPal, SSLCommerz, bKash, Wallet, Cash on Delivery, Bank Transfer
            $table->string('code', 30)->unique(); // stripe, paypal, sslcommerz, bkash, wallet, cod, bank_transfer
            $table->string('logo')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_test_mode')->default(true);
            $table->json('credentials')->nullable(); // Encrypted / sensitive API keys, secrets, public keys
            $table->json('currencies')->nullable(); // Supported currency codes e.g. ['USD', 'EUR', 'BDT']
            $table->decimal('charge_fixed', 10, 2)->default(0.00); // Fixed transaction fee
            $table->decimal('charge_percentage', 5, 2)->default(0.00); // Percentage transaction fee
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('is_active');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_gateways');
    }
};
