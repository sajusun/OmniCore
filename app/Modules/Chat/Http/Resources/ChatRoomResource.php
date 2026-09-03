<?php

namespace App\Modules\Chat\Http\Resources;

use App\Http\Resources\UserResource;
use App\Modules\Chat\Enums\ChatRoomTypeEnum;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatRoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        $authId = auth('api')->id();

        // Compute dynamic name/avatar for direct messages (single room type)
        $roomName  = $this->name;
        $roomImage = $this->image;

        if ($this->type === ChatRoomTypeEnum::SINGLE) {
            $otherParticipant = $this->users->first(fn($user) => $user->id !== $authId);
            if ($otherParticipant) {
                $roomName  = $otherParticipant->name;
                $roomImage = $otherParticipant->avatar;
            }
        }

        // Fetch current user participant info
        $myParticipant = $this->participants->firstWhere('user_id', $authId);

        // Fetch latest message
        $latestMsg = $this->messages()->latest()->first();

        // Unread messages count
        $unreadCount = 0;
        if ($myParticipant) {
            $lastReadId = $myParticipant->last_read_message_id ?? 0;
            $unreadCount = $this->messages()->where('id', '>', $lastReadId)->where('sender_id', '!=', $authId)->count();
        }

        return [
            'id'             => $this->id,
            'type'           => $this->type,
            'name'           => $roomName,
            'description'    => $this->description,
            'image'          => $roomImage ? (filter_var($roomImage, FILTER_VALIDATE_URL) ? $roomImage : url($roomImage)) : null,
            'unread_count'   => $unreadCount,
            'is_muted'       => $myParticipant ? $myParticipant->isMuted() : false,
            'mute_until'     => $myParticipant?->mute_until?->toIso8601String(),
            'pinned_count'   => $this->pinnedMessages()->count(),
            'created_by'     => $this->created_by,
            'creator'        => new UserResource($this->whenLoaded('creator')),
            'participants'   => ParticipantResource::collection($this->whenLoaded('participants')),
            'latest_message' => $latestMsg ? new MessageResource($latestMsg->load(['sender', 'media', 'reactions'])) : null,
            'created_at'     => $this->created_at ? $this->created_at->toIso8601String() : null,
            'updated_at'     => $this->updated_at ? $this->updated_at->toIso8601String() : null,
        ];
    }
}
