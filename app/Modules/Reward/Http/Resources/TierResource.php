<?php

namespace App\Modules\Reward\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TierResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'min_points' => (int) $this->min_points,
            'point_multiplier' => (float) $this->point_multiplier,
            'discount_percent' => (float) $this->discount_percent,
            'perks' => $this->perks ?? [],
            'color' => $this->color,
        ];
    }
}
