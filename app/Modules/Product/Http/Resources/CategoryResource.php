<?php

namespace App\Modules\Product\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'icon' => $this->icon,
            'image' => $this->image,
            'order' => (int) $this->order,
            'is_active' => (bool) $this->is_active,
            'is_featured' => (bool) $this->is_featured,
            'children' => CategoryResource::collection($this->whenLoaded('children')),
            'products_count' => $this->whenCounted('products'),
        ];
    }
}
