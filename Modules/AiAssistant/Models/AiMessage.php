<?php

namespace Modules\AiAssistant\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiMessage extends Model
{
    protected $table = 'ai_messages';

    protected $fillable = [
        'conversation_id',
        'role',
        'content',
        'prompt_tokens',
        'completion_tokens',
        'total_duration_ms',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(AiConversation::class, 'conversation_id');
    }
}
