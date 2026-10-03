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
        Schema::table('user_totp_credentials', function (Blueprint $table) {
            if (!Schema::hasColumn('user_totp_credentials', 'always_require_on_login')) {
                $table->boolean('always_require_on_login')->default(false)->after('is_enabled');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_totp_credentials', function (Blueprint $table) {
            if (Schema::hasColumn('user_totp_credentials', 'always_require_on_login')) {
                $table->dropColumn('always_require_on_login');
            }
        });
    }
};
