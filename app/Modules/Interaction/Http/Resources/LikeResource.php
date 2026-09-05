<?php

namespace App\Modules\Interaction\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LikeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
                'avatar' => $this->user?->avatar,
            ],
            'type' => $this->type,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
