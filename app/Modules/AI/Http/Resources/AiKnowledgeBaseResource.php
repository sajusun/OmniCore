<?php

namespace App\Modules\AI\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AiKnowledgeBaseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category' => $this->category,
            'question' => $this->question,
            'answer' => $this->answer,
            'keywords' => $this->keywords,
            'tags' => $this->tags,
            'hit_count' => $this->hit_count,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
