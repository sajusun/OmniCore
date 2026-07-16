<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verifications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            $table->enum('verification_type', ['otp', 'token',])->default('otp');
            $table->enum('purpose', ['email_verification', 'password_reset',])->default('email_verification');
            $table->string('code');

            $table->unsignedTinyInteger('attempts')->default(0);        // Verify attempts
            $table->unsignedTinyInteger('request_count')->default(1);   // Send/Resend count
            $table->timestamp('last_requested_at')->nullable();         // Last OTP/Token sent time
            $table->timestamp('blocked_until')->nullable();             // Resend blocked until

            $table->timestamp('expires_at');
            $table->timestamp('verified_at')->nullable();

            $table->enum('status', ['pending', 'verified', 'expired',])->default('pending');
            $table->timestamps();

            $table->index(['user_id', 'purpose']);
            $table->index(['code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verifications');
    }
};
