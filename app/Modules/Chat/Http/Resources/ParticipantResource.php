<?php

namespace App\Modules\Chat\Http\Resources;

use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ParticipantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id'                   => $this->id,
            'user'                 => new UserResource($this->whenLoaded('user')),
            'role'                 => $this->role,
            'joined_at'            => $this->joined_at ? $this->joined_at->toIso8601String() : null,
            'last_read_message_id' => $this->last_read_message_id,
            'last_read_at'         => $this->last_read_at ? $this->last_read_at->toIso8601String() : null,
            // Settings are private to the requesting participant themselves
            'settings'             => $this->when($this->user_id === auth('api')->id(), function () {
                return [
                    'notification_enabled' => $this->notification_enabled,
                    'sound_enabled'        => $this->sound_enabled,
                    'mute_until'           => $this->mute_until ? $this->mute_until->toIso8601String() : null,
                    'custom_settings'      => $this->settings,
                ];
            }),
        ];
    }
}
