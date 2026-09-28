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
        Schema::create('device_login_logs', function (Blueprint $table) {
            $table->id();
            $table->morphs('authenticatable', 'dev_logs_auth_idx');
            $table->foreignId('device_id')->nullable()->constrained('user_devices')->nullOnDelete();
            $table->string('ip_address', 45);
            $table->string('location')->nullable();
            $table->string('device_name')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('status', 30); // trusted_login, challenge_issued, challenge_passed, challenge_failed, revoked
            $table->string('failure_reason')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['authenticatable_type', 'authenticatable_id', 'created_at'], 'device_logs_user_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_login_logs');
    }
};
