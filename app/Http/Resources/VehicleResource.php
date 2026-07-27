<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'name' => $this->name,

            'brand' => $this->brand?->label() ?? NULL,

            'model' => $this->model,
            'year' => $this->year,

            'vehicle_type' => $this->vehicle_type?->label() ?? null,

            'transmission' => $this->transmission?->label() ?? null,


            'drive_type' => $this->drive_type?->label() ?? null,

            'horsepower' => $this->horsepower,
            'mileage' => $this->mileage,

            'engine' => $this->engine,
            'color' => $this->color,
            // 'vin' => $this->vin,

            'performance_mods' => $this->performance_mods,
            'exterior_mods' => $this->exterior_mods,
            'suspension' => $this->suspension,

            'build_story' => $this->build_story,

            'parts' => VehiclePartResource::collection($this->whenLoaded('parts')),

            'media' => when($this->media, MediaResource::collection($this->media), null),

            'created_at' => $this->created_at,
            'owner' => new UserResource($this->user),

        ];
    }
}
