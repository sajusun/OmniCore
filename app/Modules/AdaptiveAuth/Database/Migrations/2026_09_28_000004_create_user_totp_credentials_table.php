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
        Schema::create('user_totp_credentials', function (Blueprint $table) {
            $table->id();
            // Shorter custom index name to satisfy MySQL 64-char identifier limit
            $table->morphs('authenticatable', 'user_totp_auth_idx');
            $table->text('secret_key'); // Encrypted TOTP secret
            $table->json('recovery_codes')->nullable(); // Array of hashed one-time backup recovery codes
            $table->boolean('is_enabled')->default(false);
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_totp_credentials');
    }
};
