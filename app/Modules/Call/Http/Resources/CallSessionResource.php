<?php

namespace App\Modules\Call\Http\Resources;

use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CallSessionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'uuid'         => $this->uuid,
            'caller_id'    => $this->caller_id,
            'caller'       => new UserResource($this->whenLoaded('caller')),
            'chat_room_id' => $this->chat_room_id,
            'type'         => $this->type->value ?? $this->type,
            'status'       => $this->status->value ?? $this->status,
            'channel_name' => $this->channel_name,
            'started_at'   => $this->started_at?->toIso8601String(),
            'ended_at'     => $this->ended_at?->toIso8601String(),
            'duration'     => $this->duration,
            'participants' => CallParticipantResource::collection($this->whenLoaded('participants')),
            'created_at'   => $this->created_at?->toIso8601String(),
        ];
    }
}
