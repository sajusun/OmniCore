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
        // Compute dynamic name/avatar for direct messages (single room type)
        $roomName  = $this->name;
        $roomImage = $this->image;

        if ($this->type === ChatRoomTypeEnum::SINGLE) {
            $otherParticipant = $this->users->first(fn($user) => $user->id !== auth('api')->id());
            if ($otherParticipant) {
                $roomName  = $otherParticipant->name;
                $roomImage = $otherParticipant->avatar;
            }
        }

        // Fetch latest message if loaded
        $latestMsg = $this->messages()->latest()->first();

        return [
            'id'             => $this->id,
            'type'           => $this->type,
            'name'           => $roomName,
            'description'    => $this->description,
            'image'          => $roomImage ? (filter_var($roomImage, FILTER_VALIDATE_URL) ? $roomImage : url($roomImage)) : null,
            'created_by'     => $this->created_by,
            'creator'        => new UserResource($this->whenLoaded('creator')),
            'participants'   => ParticipantResource::collection($this->whenLoaded('participants')),
            'latest_message' => $latestMsg ? new MessageResource($latestMsg->load(['sender', 'media'])) : null,
            'created_at'     => $this->created_at ? $this->created_at->toIso8601String() : null,
            'updated_at'     => $this->updated_at ? $this->updated_at->toIso8601String() : null,
        ];
    }
}
