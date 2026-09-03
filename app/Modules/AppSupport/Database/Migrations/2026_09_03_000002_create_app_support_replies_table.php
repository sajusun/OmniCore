<?php

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
        if (!Schema::hasTable('app_support_replies')) {
            Schema::create('app_support_replies', function (Blueprint $table) {
                $table->id();
                $table->foreignId('app_support_id')->constrained('app_supports')->onDelete('cascade');
                $table->enum('sender_type', ['system', 'admin', 'user'])->default('admin')->index();
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->text('message');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_support_replies');
    }
};
