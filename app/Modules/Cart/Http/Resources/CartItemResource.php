<?php

namespace App\Modules\Cart\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'variant_id' => $this->variant_id,
            'name' => $this->product?->name,
            'slug' => $this->product?->slug,
            'sku' => $this->variant ? $this->variant->sku : $this->product?->sku,
            'thumbnail' => $this->product?->thumbnail_url,
            'unit_price' => (float) $this->unit_price,
            'quantity' => (int) $this->quantity,
            'subtotal' => (float) $this->subtotal,
            'variant' => $this->whenLoaded('variant', function () {
                if (! $this->variant) {
                    return null;
                }

                return [
                    'id' => $this->variant->id,
                    'sku' => $this->variant->sku,
                    'image' => $this->variant->image_url,
                    'attributes' => $this->variant->attributeValues?->map(fn ($v) => [
                        'name' => $v->attribute?->name,
                        'value' => $v->value,
                    ]),
                ];
            }),
        ];
    }
}
