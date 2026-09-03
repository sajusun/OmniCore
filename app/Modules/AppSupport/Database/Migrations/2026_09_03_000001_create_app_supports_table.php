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
        if (!Schema::hasTable('app_supports')) {
            Schema::create('app_supports', function (Blueprint $table) {
                $table->id();
                $table->string('ticket_no')->unique()->index();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('subject');
                $table->string('category')->default('other')->index();
                $table->text('message');
                $table->string('device_os')->nullable();
                $table->string('device_model')->nullable();
                $table->string('app_version')->nullable();
                $table->enum('status', ['pending', 'in_progress', 'replied', 'resolved', 'closed'])->default('pending')->index();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_supports');
    }
};
