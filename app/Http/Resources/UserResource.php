<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name ?? null,
            'email' => $this->email ?? null,
            'avatar' => $this->avatar ? url($this->avatar) : url('default/profile.png'),
            'last_activity_at' => $this->last_activity_at ? $this->last_activity_at->diffForHumans() : null,
            'is_online' => $this->when($this->is_online, $this->is_online, null),
            'profile' => $this->when($this->profile, new ProfileResource($this->profile), null),
            'info' => $this->when($this->info, $this->info),

        ];
    }
}
