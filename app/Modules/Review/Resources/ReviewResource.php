<?php

declare(strict_types=1);

namespace App\Modules\Review\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $currentUserId = $request->user('api')?->id ?? $request->user()?->id;
        $userVote = null;
        if ($currentUserId && $this->relationLoaded('votes')) {
            $vote = $this->votes->firstWhere('user_id', $currentUserId);
            $userVote = $vote ? ($vote->is_helpful ? 'helpful' : 'unhelpful') : null;
        }

        return [
            'id'                => $this->id,
            'rating'            => (int) $this->rating,
            'title'             => $this->title,
            'comment'           => $this->comment,
            'criteria_ratings'  => $this->criteria_ratings,
            'is_verified_buyer' => (bool) $this->is_verified_buyer,
            'helpful_count'     => (int) $this->helpful_count,
            'unhelpful_count'   => (int) $this->unhelpful_count,
            'user_vote'         => $userVote,
            'vendor_reply'      => $this->vendor_reply,
            'vendor_replied_at' => $this->vendor_replied_at?->toIso8601String(),
            'user'              => [
                'id'         => $this->user?->id,
                'name'       => $this->user?->name,
                'avatar_url' => $this->user?->avatar_url,
            ],
            'media'             => ReviewMediaResource::collection($this->whenLoaded('media')),
            'created_at'        => $this->created_at?->toIso8601String(),
            'updated_at'        => $this->updated_at?->toIso8601String(),
        ];
    }
}
