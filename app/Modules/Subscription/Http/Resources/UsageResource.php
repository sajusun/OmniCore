<?php

namespace App\Modules\Subscription\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'feature_code' => $this->feature_code,
            'used' => (int) $this->used,
            'resets_at' => $this->resets_at?->toIso8601String(),
        ];
    }
}
