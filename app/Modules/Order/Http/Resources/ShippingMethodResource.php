<?php

namespace App\Modules\Order\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShippingMethodResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'cost' => (float) $this->cost,
            'free_shipping_threshold' => $this->free_shipping_threshold ? (float) $this->free_shipping_threshold : null,
            'estimated_delivery_days' => $this->estimated_delivery_days,
            'description' => $this->description,
            'is_active' => (bool) $this->is_active,
        ];
    }
}
