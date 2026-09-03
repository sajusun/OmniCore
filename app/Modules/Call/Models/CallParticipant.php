<?php

namespace App\Modules\Call\Models;

use App\Models\User;
use App\Modules\Call\Enums\ParticipantCallStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CallParticipant extends Model
{
    protected $fillable = [
        'call_session_id',
        'user_id',
        'status',
        'joined_at',
        'left_at',
        'duration',
        'is_muted',
        'is_video_enabled',
        'is_screen_sharing',
    ];

    protected $casts = [
        'status'            => ParticipantCallStatusEnum::class,
        'joined_at'         => 'datetime',
        'left_at'           => 'datetime',
        'duration'          => 'integer',
        'is_muted'          => 'boolean',
        'is_video_enabled'  => 'boolean',
        'is_screen_sharing' => 'boolean',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(CallSession::class, 'call_session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
