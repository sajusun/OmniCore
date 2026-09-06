<?php

namespace App\Modules\Notification\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->title,
            'body' => $this->body,
            'reference_type' => $this->reference_type,
            'reference_id' => $this->reference_id,
            'action' => $this->action,
            'link' => $this->link,
            'meta' => $this->meta,
            'is_read' => ! is_null($this->read_at),
            'read_at' => $this->read_at,
            'created_at' => $this->created_at,
            'created_at_human' => $this->created_at?->diffForHumans(),
        ];
    }
}
