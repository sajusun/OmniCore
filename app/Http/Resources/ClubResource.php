<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClubResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'type'            => $this->type,
            'club_type_label' => $this->club_type_label,
            'country'         => $this->country,
            'state'           => $this->state,
            'city'            => $this->city,
            'description'     => $this->description,
            'status'          => $this->status,
            'created_by'      => $this->created_by,
            'creator'         => new UserResource($this->whenLoaded('creator')),
            
            // Media collections mapped to MediaResource
            'thumbnail'       => $this->thumbnail ? new MediaResource($this->thumbnail) : null,
            'thumbnail_url'   => $this->thumbnail_url,
            'images'          => MediaResource::collection($this->images),
            'images_urls'     => $this->images_urls,
            'videos'          => MediaResource::collection($this->videos),
            'videos_urls'     => $this->videos_urls,
            
            'created_at'      => $this->created_at ? $this->created_at->toIso8601String() : null,
            'updated_at'      => $this->updated_at ? $this->updated_at->toIso8601String() : null,
        ];
    }
}
