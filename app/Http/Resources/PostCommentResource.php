<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostCommentResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'comment' => $this->comment,
            'user' => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
                'username' => $this->user?->username,
                'avatar' => $this->user?->avatar,
            ],
            'statistics' => [
                'likes' => $this->likes_count ?? $this->likes()->count(),
                'replies' => $this->replies_count ?? $this->replies()->count(),
            ],
            'is_liked' => auth('api')->check() ? $this->likes->contains('user_id', auth('api')->id()) : false,
            'edited' => ! is_null($this->edited_at),
            'edited_at' => $this->edited_at,
            'created_at' => $this->created_at,
            'created_at_human' => $this->created_at?->diffForHumans(),
            'replies' => PostCommentResource::collection(
                $this->whenLoaded('replies')
            ),
        ];
    }
}
