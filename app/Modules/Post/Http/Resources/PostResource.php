<?php

namespace App\Modules\Post\Http\Resources;

use App\Http\Resources\FriendResource;
use App\Http\Resources\MediaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $authUser = auth('api')->user();

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'content' => $this->content ?? null,
            'type' => $this->type,
            'visibility' => $this->visibility,
            'friend_ids' => when($this->visibleUsers, $this->visibleUsers->pluck('id')->values()),
            'friends' => FriendResource::collection($this->whenLoaded('visibleUsers') ?? []),
            'status' => $this->status,

            'user' => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
                'username' => $this->user?->username,
                'avatar' => $this->user?->avatar ? url($this->user->avatar) : null,
            ],

            'media' => MediaResource::collection($this->whenLoaded('media')),

            'shared_post' => new self($this->whenLoaded('sharedPost')),

            'statistics' => [
                'likes' => $this->likes_count ?? $this->likes()->count(),
                'comments' => $this->comments_count ?? $this->comments()->count(),
                'shares' => $this->shares_count ?? $this->shares()->count(),
                'views' => $this->views_count ?? $this->views()->count(),
            ],
            'share_link' => $this->share_link ? route('post.share', $this->share_link) : null,

            'is_liked' => auth('api')->check() ? $this->likes->contains('user_id', auth('api')->id()) : false,
            'is_saved' => auth('api')->check() ? $this->saves->contains('user_id', auth('api')->id()) : false,
            'is_following' => $authUser && $this->user ? $authUser->isFollowing($this->user) : false,

            'created_at' => $this->created_at,
            'created_at_human' => $this->created_at?->diffForHumans(),
        ];
    }
}
