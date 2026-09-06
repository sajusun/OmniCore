<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdrawal_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 16, 2);
            $table->decimal('fee', 16, 2)->default(0.00);
            $table->decimal('net_amount', 16, 2);
            $table->string('currency', 10)->default('USD');
            $table->string('method', 50); // bank_transfer, bkash, nagad, paypal, crypto
            $table->json('account_details'); // Bank name, account number, routing, or phone/email
            $table->string('status', 20)->default('pending'); // pending, approved, rejected, canceled
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->text('admin_note')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->string('transaction_proof')->nullable(); // Slip or reference ID
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdrawal_requests');
    }
};
