<?php

namespace App\Modules\Subscription\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'user_id' => $this->user_id,
            'status' => $this->status?->value ?? $this->status,
            'is_active' => $this->isActive(),
            'on_trial' => $this->onTrial(),
            'on_grace_period' => $this->onGracePeriod(),
            'trial_ends_at' => $this->trial_ends_at?->toIso8601String(),
            'starts_at' => $this->starts_at?->toIso8601String(),
            'ends_at' => $this->ends_at?->toIso8601String(),
            'canceled_at' => $this->canceled_at?->toIso8601String(),
            'auto_renew' => (bool) $this->auto_renew,
            'payment_method' => $this->payment_method,
            'plan' => new PlanResource($this->whenLoaded('plan', $this->plan)),
            'usages' => UsageResource::collection($this->whenLoaded('usages', $this->usages)),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
