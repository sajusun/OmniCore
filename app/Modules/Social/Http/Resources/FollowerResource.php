<?php

namespace App\Modules\Social\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FollowerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $authUser = auth('api')->user() ?? auth()->user();

        return [
            'id' => $this->id,
            'name' => $this->name ?? null,
            'username' => $this->username ?? null,
            'avatar' => !empty($this->avatar) ? url($this->avatar) : null,
            'is_following' => ($authUser && method_exists($authUser, 'isFollowing')) ? $authUser->isFollowing($this->id) : false,
            'is_friend' => ($authUser && method_exists($authUser, 'isFriend')) ? $authUser->isFriend($this->id) : false,
            'mutual_friends_count' => ($authUser && method_exists($authUser, 'getMutualFriendsCount')) ? $authUser->getMutualFriendsCount($this->id) : 0,
        ];
    }
}
