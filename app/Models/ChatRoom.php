<?php

namespace App\Models;

use App\Enums\Chat\ChatRoomTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chat_participants', 'chat_room_id', 'user_id')
            ->withPivot(['role', 'joined_at', 'last_read_message_id', 'last_read_at', 'notification_enabled', 'sound_enabled', 'mute_until', 'settings'])
            ->withTimestamps();
    }
}
