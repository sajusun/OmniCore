<?php

namespace App\Modules\AI\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AiConversationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'persona' => $this->persona,
            'provider' => $this->provider,
            'tokens_used' => $this->tokens_used,
            'is_starred' => $this->is_starred,
            'messages' => AiMessageResource::collection($this->whenLoaded('messages')),
            'latest_message' => new AiMessageResource($this->whenLoaded('latestMessage')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
