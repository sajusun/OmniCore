<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('firebase_tokens', function (Blueprint $table) {
            $table->string('device_name')->nullable()->after('device_id');
            $table->string('platform')->nullable()->after('device_name');
            $table->longText('jwt_token')->nullable()->after('platform');
            $table->string('ip_address', 45)->nullable()->after('jwt_token');
            $table->text('user_agent')->nullable()->after('ip_address');
            $table->timestamp('last_activity_at')->nullable()->after('user_agent');
        });
    }

    public function down(): void
    {
        Schema::table('firebase_tokens', function (Blueprint $table) {
            $table->dropColumn([
                'device_name',
                'platform',
                'jwt_token',
                'ip_address',
                'user_agent',
                'last_activity_at',
            ]);
        });
    }
};
