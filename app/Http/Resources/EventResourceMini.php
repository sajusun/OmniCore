<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventResourceMini extends JsonResource
{
    public function toArray($request): array
    {
        $authId = auth('api')->id();

        return [
            'id'                       => $this->id,
            'user_id'                  => $this->user_id,

            'title'                    => $this->title,

            'event_type'               => $this->event_type,
            'event_type_label'         => $this->event_type_label,

            'location'                 => $this->location,

            'event_date'               => $this->event_date ? $this->event_date->toDateString() : null,
            'event_time'               => $this->event_time,

            'is_public'                => $this->is_public,
            'status'                   => $this->status,

            'thumbnail_url'            => $this->thumbnail_url,

            // RSVP stats
            'going_count'              => $this->rsvps()->where('status', 'going')->count(),
            'interested_count'         => $this->rsvps()->where('status', 'interested')->count(),
            'user_rsvp_status'         => $authId ? $this->getUserRsvpStatus($authId) : null,

            'created_at'               => $this->created_at ? $this->created_at->toIso8601String() : null,
            'updated_at'               => $this->updated_at ? $this->updated_at->toIso8601String() : null,
        ];
    }
}
