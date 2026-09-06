<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('balance', 16, 2)->default(0.00);
            $table->decimal('frozen_balance', 16, 2)->default(0.00); // Balance reserved for pending withdrawal/escrow
            $table->string('currency', 10)->default('USD');
            $table->string('status', 20)->default('active'); // active, locked, suspended
            $table->timestamps();

            $table->unique('user_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
