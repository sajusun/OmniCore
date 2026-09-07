<?php

namespace App\Modules\Interaction\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $currentUser = $request->user();

        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
                'avatar' => $this->user?->avatar,
                'email' => $this->user?->email,
            ],
            'commentable_type' => $this->commentable_type,
            'commentable_id' => $this->commentable_id,
            'parent_id' => $this->parent_id,
            'body' => $this->body,
            'likes_count' => $this->likes_count,
            'is_liked' => $currentUser && method_exists($this->resource, 'isLikedBy') ? $this->resource->isLikedBy($currentUser) : false,
            'replies_count' => $this->replies_count,
            'replies' => CommentResource::collection($this->whenLoaded('replies')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
