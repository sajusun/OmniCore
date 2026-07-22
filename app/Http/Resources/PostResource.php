<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
// use App\Http\Resources\PostResource;
use App\Http\Resources\MediaResource;
use App\Http\Resources\FriendResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            // 'title' => $this->title ?? null,
            'slug' => $this->slug,
            'content' => $this->content ?? null,
            // 'thumbnail' => $this->thumbnail ?? null,

            'type' => $this->type,
            'visibility' => $this->visibility,
            'friend_ids' => $this->visibleUsers->pluck('id')->values(),
            'friends' => FriendResource::collection($this->whenLoaded('visibleUsers') ?? []),
            'status' => $this->status,

            'user' => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
                'username' => $this->user?->username,
                'avatar' => $this->user?->avatar ? url($this->user->avatar) : null,
            ],

            'media' => MediaResource::collection($this->whenLoaded('media')),

            'shared_post' => new PostResource($this->whenLoaded('sharedPost')),

            'statistics' => [
                'likes' => $this->likes_count ?? $this->likes()->count(),
                'comments' => $this->comments_count ?? $this->comments()->count(),
                'shares' => $this->shares_count ?? $this->shares()->count(),
                'views' => $this->views_count ?? $this->views()->count(),
            ],
            'share_link' => $this->share_link ? route('post.share', $this->share_link) : null,

            'is_liked' => auth('api')->check() ? $this->likes->contains('user_id', auth('api')->id()) : false,
            'is_saved' => auth('api')->check() ? $this->saves->contains('user_id', auth('api')->id()) : false,

            'created_at' => $this->created_at,
            'created_at_human' => $this->created_at?->diffForHumans(),
        ];
    }
}
