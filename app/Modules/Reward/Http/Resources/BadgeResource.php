<?php

namespace App\Modules\Reward\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BadgeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'icon' => $this->icon,
            'badge_type' => $this->badge_type,
            'points_reward' => (int) $this->points_reward,
            'criteria_type' => $this->criteria_type,
            'criteria_threshold' => (int) $this->criteria_threshold,
            'unlocked_at' => $this->pivot?->awarded_at?->toIso8601String(),
        ];
    }
}
