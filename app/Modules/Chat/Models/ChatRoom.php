<?php

namespace App\Modules\Chat\Models;

use App\Models\User;
use App\Modules\Chat\Enums\ChatRoomTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatRoom extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'type',
        'name',
        'description',
        'image',
        'created_by',
    ];

    protected $casts = [
        'type' => ChatRoomTypeEnum::class,
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ChatParticipant::class, 'chat_room_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'chat_room_id');
    }

    public function latestMessage(): HasMany
    {
        return $this->hasMany(Message::class, 'chat_room_id')->latest();
    }

    public function pinnedMessages(): HasMany
    {
        return $this->hasMany(PinnedMessage::class, 'chat_room_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chat_participants', 'chat_room_id', 'user_id')
            ->withPivot(['role', 'joined_at', 'last_read_message_id', 'last_read_at', 'notification_enabled', 'sound_enabled', 'mute_until', 'settings'])
            ->withTimestamps();
    }

    /**
     * Calculate unread messages count for a specific user in this room.
     */
    public function unreadCountFor(int $userId): int
    {
        $participant = $this->participants()->where('user_id', $userId)->first();
        if (!$participant) {
            return 0;
        }

        $lastReadId = $participant->last_read_message_id ?? 0;

        return $this->messages()
            ->where('id', '>', $lastReadId)
            ->where('sender_id', '!=', $userId)
            ->count();
    }
}
