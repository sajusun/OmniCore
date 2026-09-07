<?php

namespace App\Modules\Interaction\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShareLinkResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'url' => $this->resource->getUrl(),
            'slug' => $this->slug,
            'token' => $this->token,
            'expires_at' => $this->expires_at?->toISOString(),
            'max_clicks' => $this->max_clicks,
            'click_count' => $this->click_count,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
