<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('device_login_challenges', function (Blueprint $table) {
            $table->id();
            $table->string('challenge_token', 64)->unique();
            $table->morphs('authenticatable', 'dev_chal_auth_idx');
            $table->string('device_uuid', 64)->index();
            $table->json('device_metadata')->nullable();
            $table->string('otp_code_hash');
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->unsignedTinyInteger('max_attempts')->default(3);
            $table->timestamp('resend_available_at')->nullable();
            $table->timestamp('expires_at');
            $table->timestamp('verified_at')->nullable();
            $table->boolean('remember_device')->default(true);
            $table->timestamps();

            $table->index(['challenge_token', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_login_challenges');
    }
};
