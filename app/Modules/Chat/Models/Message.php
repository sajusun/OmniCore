<?php

namespace App\Modules\Chat\Models;

use App\Models\User;
use App\Modules\Chat\Enums\MessageTypeEnum;
use App\Modules\Media\Traits\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes, HasMedia;

    protected $fillable = [
        'chat_room_id',
        'sender_id',
        'message_type',
        'message',
        'reply_to',
        'is_edited',
        'edited_at',
    ];

    protected $casts = [
        'message_type' => MessageTypeEnum::class,
        'is_edited'    => 'boolean',
        'edited_at'    => 'datetime',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(ChatRoom::class, 'chat_room_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function replyMessage(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'reply_to');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(MessageReaction::class, 'message_id');
    }

    public function pinned(): HasOne
    {
        return $this->hasOne(PinnedMessage::class, 'message_id');
    }

    public function isPinned(): bool
    {
        return $this->pinned()->exists();
    }

    /**
     * Get grouped reactions summary e.g. ['like' => 5, 'love' => 2]
     */
    public function getReactionSummaryAttribute(): array
    {
        return $this->reactions()
            ->selectRaw('reaction, count(*) as count')
            ->groupBy('reaction')
            ->pluck('count', 'reaction')
            ->toArray();
    }
}
