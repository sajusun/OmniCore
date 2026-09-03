<?php

namespace App\Modules\Call\Http\Resources;

use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CallParticipantResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                => $this->id,
            'user_id'           => $this->user_id,
            'user'              => new UserResource($this->whenLoaded('user')),
            'status'            => $this->status->value ?? $this->status,
            'joined_at'         => $this->joined_at?->toIso8601String(),
            'left_at'           => $this->left_at?->toIso8601String(),
            'duration'          => $this->duration,
            'is_muted'          => $this->is_muted,
            'is_video_enabled'  => $this->is_video_enabled,
            'is_screen_sharing' => $this->is_screen_sharing,
        ];
    }
}
