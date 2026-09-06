<?php

namespace App\Modules\Subscription\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => (float) $this->price,
            'formatted_price' => number_format((float) $this->price, 2) . ' ' . $this->currency,
            'signup_fee' => (float) $this->signup_fee,
            'currency' => $this->currency,
            'billing_interval' => $this->billing_interval,
            'interval_count' => (int) $this->interval_count,
            'trial_days' => (int) $this->trial_days,
            'is_popular' => (bool) $this->is_popular,
            'is_free' => $this->isFree(),
            'features' => PlanFeatureResource::collection($this->whenLoaded('features', $this->features)),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
