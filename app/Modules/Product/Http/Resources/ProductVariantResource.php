<?php

namespace App\Modules\Product\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'barcode' => $this->barcode,
            'price' => (float) $this->price,
            'compare_at_price' => $this->compare_at_price ? (float) $this->compare_at_price : null,
            'stock_quantity' => $this->manage_stock ? (int) $this->stock_quantity : null,
            'is_in_stock' => $this->isInStock(),
            'is_active' => (bool) $this->is_active,
            'image_url' => $this->image_url,
            'weight' => $this->weight,
            'dimensions' => $this->dimensions,
            'attributes' => $this->whenLoaded('attributeValues', function () {
                return $this->attributeValues->map(fn ($val) => [
                    'attribute_id' => $val->attribute_id,
                    'attribute_name' => $val->attribute?->name,
                    'attribute_slug' => $val->attribute?->slug,
                    'value_id' => $val->id,
                    'value' => $val->value,
                    'code' => $val->code,
                ]);
            }),
        ];
    }
}
