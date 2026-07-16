<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('food_logs', function (Blueprint $table) {
            $table->enum('source_type', ['scan', 'search', 'manual'])->default('manual')->after('user_id');
            $table->foreignId('product_id')->nullable()->after('source_type')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('food_logs', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn(['product_id','source_type',]);
        });
    }
};
