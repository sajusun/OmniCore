<?php

namespace App\Modules\Social\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FriendResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $authId = auth('api')->id() ?? auth()->id();

        // Check if $this is a User model or a Friend/FriendRequest model
        $targetUser = $this;
        if (isset($this->sender_id) && isset($this->receiver_id)) {
            $targetUser = ($this->sender_id === $authId) ? $this->receiver : $this->sender;
        }

        $targetUserId = $targetUser->id ?? $this->id ?? null;
        $mutualCount = ($authId && $targetUserId && method_exists(auth('api')->user() ?? auth()->user(), 'getMutualFriendsCount'))
            ? (auth('api')->user() ?? auth()->user())->getMutualFriendsCount($targetUserId)
            : 0;

        return [
            'id' => $targetUser->id ?? $this->id,
            'name' => $targetUser->name ?? null,
            'username' => $targetUser->username ?? null,
            'avatar' => !empty($targetUser->avatar) ? url($targetUser->avatar) : null,
            'mutual_friends_count' => $mutualCount,
            'status' => $this->when(isset($this->status), $this->status),
            'requested_at' => $this->when(isset($this->status), $this->created_at?->diffForHumans()),
            'friends_since' => $this->when($this->pivot, $this->pivot?->created_at?->diffForHumans()),
        ];
    }
}
