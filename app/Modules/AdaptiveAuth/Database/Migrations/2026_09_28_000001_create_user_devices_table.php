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
        Schema::create('user_devices', function (Blueprint $table) {
            $table->id();
            $table->morphs('authenticatable'); // authenticatable_type, authenticatable_id
            $table->string('device_uuid', 64)->index();
            $table->string('device_name')->default('Unknown Device');
            $table->string('platform', 50)->nullable(); // e.g. Windows, iOS, macOS, Android
            $table->string('browser', 50)->nullable();  // e.g. Chrome, Safari, Firefox
            $table->string('device_type', 30)->default('desktop'); // desktop, mobile, tablet
            $table->string('fingerprint_hash', 64)->nullable()->index();
            $table->string('last_ip', 45);
            $table->string('city', 100)->nullable();
            $table->string('region', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('country_code', 10)->nullable();
            $table->boolean('is_trusted')->default(true);
            $table->timestamp('trusted_at')->nullable();
            $table->timestamp('trusted_until')->nullable();
            $table->timestamp('last_active_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();

            $table->index(['authenticatable_type', 'authenticatable_id', 'device_uuid'], 'user_device_lookup_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_devices');
    }
};
