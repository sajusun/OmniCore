<?php

namespace Modules\AiAssistant\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiPersona extends Model
{
    protected $table = 'ai_personas';

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'icon',
        'model',
        'system_prompt',
        'temperature',
        'is_system_default',
    ];

    protected $casts = [
        'temperature' => 'float',
        'is_system_default' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model', 'App\\Models\\User'));
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(AiConversation::class, 'persona_id');
    }
}
