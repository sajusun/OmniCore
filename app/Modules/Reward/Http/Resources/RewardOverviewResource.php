<?php

namespace App\Modules\Reward\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RewardOverviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'points_balance' => (int) $this->points_balance,
            'lifetime_points' => (int) $this->lifetime_points,
            'streak_days' => (int) $this->streak_days,
            'can_checkin_today' => $this->canCheckInToday(),
            'last_checkin_at' => $this->last_checkin_at?->toIso8601String(),
            'tier' => new TierResource($this->whenLoaded('tier', $this->tier)),
        ];
    }
}
