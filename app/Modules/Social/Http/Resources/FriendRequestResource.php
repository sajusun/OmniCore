<?php

namespace App\Modules\Social\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FriendRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $authId = auth('api')->id() ?? auth()->id();
        $isOutgoing = ($this->sender_id === $authId);
        $counterpart = $isOutgoing ? $this->receiver : $this->sender;

        $counterpartId = $counterpart->id ?? null;
        $mutualCount = ($authId && $counterpartId && method_exists(auth('api')->user() ?? auth()->user(), 'getMutualFriendsCount'))
            ? (auth('api')->user() ?? auth()->user())->getMutualFriendsCount($counterpartId)
            : 0;

        return [
            'request_id' => $this->id,
            'direction' => $isOutgoing ? 'outgoing' : 'incoming',
            'status' => is_object($this->status) ? $this->status->value : $this->status,
            'user' => [
                'id' => $counterpart->id ?? null,
                'name' => $counterpart->name ?? null,
                'username' => $counterpart->username ?? null,
                'avatar' => !empty($counterpart->avatar) ? url($counterpart->avatar) : null,
                'mutual_friends_count' => $mutualCount,
            ],
            'created_at' => $this->created_at,
            'created_at_human' => $this->created_at?->diffForHumans(),
        ];
    }
}
