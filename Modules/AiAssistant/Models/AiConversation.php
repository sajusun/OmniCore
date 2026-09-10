<?php

namespace Modules\AiAssistant\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiConversation extends Model
{
    protected $table = 'ai_conversations';

    protected $fillable = [
        'user_id',
        'persona_id',
        'title',
        'model_used',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model', 'App\\Models\\User'));
    }

    public function persona(): BelongsTo
    {
        return $this->belongsTo(AiPersona::class, 'persona_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(AiMessage::class, 'conversation_id')->orderBy('created_at', 'asc');
    }

    /**
     * Sliding window history loader
     */
    public function getContextMessages(int $limit = 10): array
    {
        return $this->messages()
            ->latest('id')
            ->limit($limit)
            ->get()
            ->reverse()
            ->map(fn(AiMessage $msg) => [
                'role' => $msg->role,
                'content' => $msg->content,
            ])
            ->values()
            ->toArray();
    }
}
