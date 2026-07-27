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

            'brand' => [
                'value' => $this->brand?->value,
                'label' => $this->brand?->label(),
            ],

            'model' => $this->model,
            'year' => $this->year,

            'vehicle_type' => [
                'value' => $this->vehicle_type?->value,
                'label' => $this->vehicle_type?->label(),
            ],

            'transmission' => [
                'value' => $this->transmission?->value,
                'label' => $this->transmission?->label(),
            ],

            'drive_type' => [
                'value' => $this->drive_type?->value,
                'label' => $this->drive_type?->label(),
            ],

            'horsepower' => $this->horsepower,
            'mileage' => $this->mileage,

            'engine' => $this->engine,
            'color' => $this->color,
            'vin' => $this->vin,

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
