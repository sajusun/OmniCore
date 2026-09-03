<?php

namespace App\Modules\Chat\Models;

use App\Models\User;
use App\Modules\Chat\Enums\ParticipantRoleEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatParticipant extends Model
{
    protected $fillable = [
        'chat_room_id',
        'user_id',
        'role',
        'joined_at',
        'last_read_message_id',
        'last_read_at',
        'notification_enabled',
        'sound_enabled',
        'mute_until',
        'settings',
    ];

    protected $casts = [
        'role'                 => ParticipantRoleEnum::class,
        'joined_at'            => 'datetime',
        'last_read_at'         => 'datetime',
        'mute_until'           => 'datetime',
        'notification_enabled' => 'boolean',
        'sound_enabled'        => 'boolean',
        'settings'             => 'array',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(ChatRoom::class, 'chat_room_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function lastReadMessage(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'last_read_message_id');
    }

    public function isMuted(): bool
    {
        return $this->mute_until && $this->mute_until->isFuture();
    }
}
