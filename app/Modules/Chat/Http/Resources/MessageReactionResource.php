<?php

namespace App\Modules\Chat\Http\Resources;

use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageReactionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'message_id' => $this->message_id,
            'user_id'    => $this->user_id,
            'user'       => new UserResource($this->whenLoaded('user')),
            'reaction'   => $this->reaction,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
