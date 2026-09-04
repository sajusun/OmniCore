<?php

namespace App\Modules\Product\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'rating' => (int) $this->rating,
            'title' => $this->title,
            'comment' => $this->comment,
            'is_verified_purchase' => (bool) $this->is_verified_purchase,
            'status' => $this->status,
            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'avatar' => $this->user->avatar,
            ]),
            'photos' => $this->whenLoaded('media', function () {
                return $this->media->map(fn ($m) => [
                    'id' => $m->id,
                    'url' => $m->url,
                ]);
            }),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
