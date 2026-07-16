<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id')->nullable()->after('email');
            $table->string('apple_id')->nullable()->after('google_id');
            $table->string('provider')->nullable()->after('apple_id');
            $table->string('provider_id')->nullable()->after('provider');
            $table->boolean('is_social_logged')->default(false)->after('provider_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'google_id',
                'apple_id',
                'provider',
                'provider_id',
                'is_social_logged',
            ]);
        });
    }
};
