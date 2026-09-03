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
        if (!Schema::hasTable('chat_participants')) {
            Schema::create('chat_participants', function (Blueprint $table) {
                $table->id();
                $table->foreignId('chat_room_id')->constrained('chat_rooms')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('role')->default('member'); // owner, admin, member
                $table->timestamp('joined_at')->useCurrent();
                $table->foreignId('last_read_message_id')->nullable()->constrained('messages')->nullOnDelete();
                $table->timestamp('last_read_at')->nullable();
                $table->boolean('notification_enabled')->default(true);
                $table->boolean('sound_enabled')->default(true);
                $table->timestamp('mute_until')->nullable();
                $table->json('settings')->nullable(); // wallpaper, nickname, auto-delete settings, etc.
                $table->timestamps();

                $table->unique(['chat_room_id', 'user_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_participants');
    }
};
