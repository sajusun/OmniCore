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
        Schema::create('call_sessions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('caller_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('chat_room_id')->nullable()->constrained('chat_rooms')->nullOnDelete();
            $table->string('type', 30)->default('audio'); // 'audio', 'video', 'screen_share'
            $table->string('status', 30)->default('initiating'); // 'initiating', 'ringing', 'connected', 'ended', 'missed', 'rejected', 'busy'
            $table->string('channel_name')->nullable(); // Agora channel or WebRTC Room ID
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->unsignedInteger('duration')->default(0); // in seconds
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['caller_id', 'status']);
            $table->index('chat_room_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('call_sessions');
    }
};
