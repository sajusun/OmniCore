<?php

namespace App\Modules\Chat\Http\Resources;

use App\Http\Resources\MediaResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'chat_room_id'  => $this->chat_room_id,
            'sender_id'     => $this->sender_id,
            'sender'        => new UserResource($this->whenLoaded('sender')),
            'message_type'  => $this->message_type,
            'message'       => $this->message,
            'reply_to'      => $this->reply_to,
            'reply_message' => new MessageResource($this->whenLoaded('replyMessage')),
            'media'         => MediaResource::collection($this->whenLoaded('media')),
            'is_edited'     => $this->is_edited,
            'edited_at'     => $this->edited_at ? $this->edited_at->toIso8601String() : null,
            'created_at'    => $this->created_at ? $this->created_at->toIso8601String() : null,
            'updated_at'    => $this->updated_at ? $this->updated_at->toIso8601String() : null,
        ];
    }
}
