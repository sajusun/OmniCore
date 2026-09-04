<?php

namespace App\Modules\Product\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'type' => $this->type,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'price' => (float) $this->price,
            'compare_at_price' => $this->compare_at_price ? (float) $this->compare_at_price : null,
            'discount_percentage' => $this->discount_percentage,
            'is_in_stock' => (bool) $this->is_in_stock,
            'stock_quantity' => $this->manage_stock ? $this->stock_quantity : null,
            'manage_stock' => (bool) $this->manage_stock,
            'allow_backorders' => (bool) $this->allow_backorders,
            'is_featured' => (bool) $this->is_featured,
            'is_refundable' => (bool) $this->is_refundable,
            'weight' => $this->weight,
            'dimensions' => $this->dimensions,
            'views_count' => (int) $this->views_count,
            'sales_count' => (int) $this->sales_count,
            'average_rating' => (float) $this->average_rating,
            'reviews_count' => (int) $this->reviews_count,
            'thumbnail' => $this->thumbnail_url,
            'gallery' => $this->gallery_urls,
            'category' => $this->whenLoaded('category', fn () => [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
                'breadcrumbs' => $this->category->getBreadcrumbs(),
            ]),
            'brand' => $this->whenLoaded('brand', fn () => [
                'id' => $this->brand->id,
                'name' => $this->brand->name,
                'slug' => $this->brand->slug,
                'logo' => $this->brand->logo,
            ]),
            'variants' => ProductVariantResource::collection($this->whenLoaded('activeVariants')),
            'tags' => $this->whenLoaded('tags', fn () => $this->tags->pluck('name')),
            'meta' => [
                'title' => $this->meta_title ?: $this->name,
                'description' => $this->meta_description ?: $this->short_description,
            ],
            'in_wishlist' => $request->user() ? $request->user()->hasInWishlist($this->id) : false,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
