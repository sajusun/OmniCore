<?php

namespace App\Modules\AI\Traits;

use App\Modules\AI\Models\AiConversation;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasAiConversations
{
    public function aiConversations(): HasMany
    {
        return $this->hasMany(AiConversation::class)->orderBy('updated_at', 'desc');
    }
}
