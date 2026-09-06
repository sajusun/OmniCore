<?php

namespace App\Modules\Subscription\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanFeatureResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'value' => $this->value,
            'is_limited' => (bool) $this->is_limited,
            'is_unlimited' => $this->isUnlimited(),
            'quota' => $this->getQuota(),
        ];
    }
}
