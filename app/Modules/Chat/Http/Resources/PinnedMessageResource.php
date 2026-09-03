<?php

namespace App\Modules\Chat\Http\Resources;

use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PinnedMessageResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'chat_room_id' => $this->chat_room_id,
            'message_id'   => $this->message_id,
            'pinned_by'    => new UserResource($this->whenLoaded('pinnedBy')),
            'message'      => new MessageResource($this->whenLoaded('message')),
            'pinned_at'    => $this->pinned_at?->toIso8601String(),
        ];
    }
}
