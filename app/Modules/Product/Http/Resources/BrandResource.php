<?php

namespace App\Modules\Product\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'logo' => $this->logo,
            'website' => $this->website,
            'description' => $this->description,
            'is_active' => (bool) $this->is_active,
            'is_featured' => (bool) $this->is_featured,
            'products_count' => $this->whenCounted('products'),
        ];
    }
}
