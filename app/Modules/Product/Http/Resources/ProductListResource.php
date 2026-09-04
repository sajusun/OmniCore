<?php

namespace App\Modules\Product\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'type' => $this->type,
            'price' => (float) $this->price,
            'compare_at_price' => $this->compare_at_price ? (float) $this->compare_at_price : null,
            'discount_percentage' => $this->discount_percentage,
            'is_in_stock' => (bool) $this->is_in_stock,
            'stock_quantity' => $this->manage_stock ? $this->stock_quantity : null,
            'is_featured' => (bool) $this->is_featured,
            'average_rating' => (float) $this->average_rating,
            'reviews_count' => (int) $this->reviews_count,
            'thumbnail' => $this->thumbnail_url,
            'category' => $this->whenLoaded('category', fn () => [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ]),
            'brand' => $this->whenLoaded('brand', fn () => [
                'id' => $this->brand->id,
                'name' => $this->brand->name,
                'slug' => $this->brand->slug,
            ]),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
