<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_subscribed')->default(false)->after('status');
            $table->timestamp('subscription_ends_at')->nullable()->after('is_subscribed');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'is_subscribed',
                'subscription_ends_at',
            ]);
        });
    }
};
