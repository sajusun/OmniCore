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
        $authId = auth('api')->id();

        // Current user's membership row (if loaded)
        $myMembership = $authId ? $this->getMembership($authId) : null;

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

            // ── Media ──────────────────────────────────────────
            'thumbnail'       => $this->thumbnail ? new MediaResource($this->thumbnail) : null,
            'thumbnail_url'   => $this->thumbnail_url,
            'images'          => MediaResource::collection($this->images),
            'images_urls'     => $this->images_urls,
            'videos'          => MediaResource::collection($this->videos),
            'videos_urls'     => $this->videos_urls,

            // ── Membership info ────────────────────────────────
            'members_count'           => $this->memberships()->where('status', 'approved')->count(),
            'is_member'               => $authId ? $this->isMember($authId) : false,
            'is_creator'              => $authId ? $this->isCreator($authId) : false,
            'user_role'               => $myMembership?->role,
            'user_membership_status'  => $myMembership?->status,

            'created_at'      => $this->created_at?->toIso8601String(),
            'updated_at'      => $this->updated_at?->toIso8601String(),
        ];
    }
}
