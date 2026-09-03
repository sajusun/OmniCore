<?php

namespace App\Modules\Call\Models;

use App\Models\User;
use App\Modules\Call\Enums\CallStatusEnum;
use App\Modules\Call\Enums\CallTypeEnum;
use App\Modules\Chat\Models\ChatRoom;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CallSession extends Model
{
    protected $fillable = [
        'uuid',
        'caller_id',
        'chat_room_id',
        'type',
        'status',
        'channel_name',
        'started_at',
        'ended_at',
        'duration',
        'meta',
    ];

    protected $casts = [
        'type'       => CallTypeEnum::class,
        'status'     => CallStatusEnum::class,
        'started_at' => 'datetime',
        'ended_at'   => 'datetime',
        'duration'   => 'integer',
        'meta'       => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
            if (empty($model->channel_name)) {
                $model->channel_name = 'call_' . Str::random(16);
            }
        });
    }

    public function caller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'caller_id');
    }

    public function chatRoom(): BelongsTo
    {
        return $this->belongsTo(ChatRoom::class, 'chat_room_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(CallParticipant::class, 'call_session_id');
    }

    public function getReceiverAttribute(): ?User
    {
        $participant = $this->participants->where('user_id', '!=', $this->caller_id)->first();
        return $participant?->user;
    }
}
