<?php

namespace App\Modules\Cart\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'guest_token' => $this->guest_token,
            'items_count' => $this->total_items_count,
            'subtotal' => (float) $this->subtotal,
            'discount_amount' => (float) $this->discount_amount,
            'coupon_code' => $this->coupon_code,
            'grand_total' => (float) $this->grand_total,
            'items' => CartItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
