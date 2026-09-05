<?php

namespace App\Modules\Interaction\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookmarkResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'collection' => $this->collection,
            'subject_type' => $this->bookmarkable_type,
            'subject_id' => $this->bookmarkable_id,
            'item' => $this->whenLoaded('bookmarkable'),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
