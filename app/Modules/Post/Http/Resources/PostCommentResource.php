<?php

namespace App\Modules\Post\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostCommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'comment' => $this->body ?? $this->comment,
            'user' => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
                'username' => $this->user?->username,
                'avatar' => $this->user?->avatar ? url($this->user->avatar) : null,
            ],
            'statistics' => [
                'likes' => $this->likes_count ?? $this->likes()->count(),
                'replies' => $this->replies_count ?? $this->replies()->count(),
            ],
            'is_liked' => auth('api')->check() ? $this->isLikedBy(auth('api')->user()) : false,
            'edited' => ! is_null($this->edited_at),
            'edited_at' => $this->edited_at,
            'created_at' => $this->created_at,
            'created_at_human' => $this->created_at?->diffForHumans(),
            'replies' => self::collection($this->whenLoaded('replies')),
        ];
    }
}
