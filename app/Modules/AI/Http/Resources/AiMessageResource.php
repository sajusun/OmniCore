<?php

namespace App\Modules\AI\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AiMessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'role' => $this->role,
            'content' => $this->content,
            'metadata' => $this->metadata,
            'tokens' => $this->tokens,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
