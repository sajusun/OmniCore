<?php

namespace App\Modules\Call\Http\Resources;

use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CallLogResource extends JsonResource
{
    public function toArray($request): array
    {
        $authId = auth('api')->id();
        $isCaller = ($this->caller_id === $authId);
        $otherUser = $isCaller
            ? ($this->participants->firstWhere('user_id', '!=', $authId)?->user ?? $this->caller)
            : $this->caller;

        $direction = $isCaller ? 'outgoing' : 'incoming';
        $myParticipant = $this->participants->firstWhere('user_id', $authId);

        return [
            'id'           => $this->id,
            'uuid'         => $this->uuid,
            'direction'    => $direction,
            'type'         => $this->type->value ?? $this->type,
            'status'       => $this->status->value ?? $this->status,
            'is_missed'    => ($this->status->value ?? $this->status) === 'missed' || ($direction === 'incoming' && ($myParticipant?->status?->value ?? $myParticipant?->status) === 'calling'),
            'duration'     => $this->duration,
            'started_at'   => $this->started_at?->toIso8601String(),
            'ended_at'     => $this->ended_at?->toIso8601String(),
            'user'         => new UserResource($otherUser),
            'chat_room_id' => $this->chat_room_id,
            'created_at'   => $this->created_at?->toIso8601String(),
        ];
    }
}
