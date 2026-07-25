<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehiclePartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vehicle_id' => $this->vehicle_id,
            'name' => $this->name,
            'image' => $this->part_image ? new MediaResource($this->part_image) : null,
            'image_url' => $this->part_image_url,
            'created_at' => $this->created_at,
        ];
    }
}
