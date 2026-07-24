<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray($request): array
    {
        $authId = auth('api')->id();

        return [
            'id'                       => $this->id,
            'user_id'                  => $this->user_id,
            'user'                     => new UserResource($this->whenLoaded('user')),
            'club_id'                  => $this->club_id,
            'club'                     => new ClubResource($this->whenLoaded('club')),

            'title'                    => $this->title,
            'description'              => $this->description,

            'event_type'               => $this->event_type,
            'event_type_label'         => $this->event_type_label,

            'location'                 => $this->location,
            'latitude'                 => $this->latitude,
            'longitude'                => $this->longitude,

            'event_date'               => $this->event_date ? $this->event_date->toDateString() : null,
            'event_time'               => $this->event_time,

            'max_participants'         => $this->max_participants,
            'vehicles_required'        => $this->vehicles_required,
            'vehicles_required_labels' => $this->vehicles_required_labels,

            'is_public'                => $this->is_public,
            'status'                   => $this->status,

            // Media
            'thumbnail'                => $this->thumbnail ? new MediaResource($this->thumbnail) : null,
            'thumbnail_url'            => $this->thumbnail_url,
            'images'                   => MediaResource::collection($this->images),
            'images_urls'              => $this->images_urls,

            // RSVP stats
            'going_count'              => $this->rsvps()->where('status', 'going')->count(),
            'interested_count'         => $this->rsvps()->where('status', 'interested')->count(),
            'user_rsvp_status'         => $authId ? $this->getUserRsvpStatus($authId) : null,

            'created_at'               => $this->created_at ? $this->created_at->toIso8601String() : null,
            'updated_at'               => $this->updated_at ? $this->updated_at->toIso8601String() : null,
        ];
    }
}
