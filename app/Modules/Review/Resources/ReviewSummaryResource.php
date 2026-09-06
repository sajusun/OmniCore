<?php

declare(strict_types=1);

namespace App\Modules\Review\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'average_rating'      => (float) ($this['average_rating'] ?? 0.0),
            'total_reviews'       => (int) ($this['total_reviews'] ?? 0),
            'rating_distribution' => $this['rating_distribution'] ?? [],
            'has_media_count'     => (int) ($this['has_media_count'] ?? 0),
        ];
    }
}
