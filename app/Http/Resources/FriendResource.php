<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FriendResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $this->sender_id === auth('api')->id() ? $this->receiver : ($this->receiver_id === auth('api')->id() ? $this->sender : $this);

        return [
            'id' => $this->id,
            'user' => [
                'user_id' => $user->id,
                'name' => $user->name ?? null,
                'avatar' => $user->avatar ? url($user->avatar) : null,
            ],

            'status' => $this->when(isset($this->status), $this->status),

            'requested_at' => $this->when(isset($this->status), $this->created_at?->diffForHumans()),

            'friends_since' => $this->when($this->pivot, $this->pivot?->created_at?->diffForHumans()),
        ];
    }
}
