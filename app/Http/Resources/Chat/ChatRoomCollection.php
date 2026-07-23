<?php

namespace App\Http\Resources\Chat;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ChatRoomCollection extends ResourceCollection
{
    /**
     * The resource that this collection collects.
     *
     * @var string
     */
    public $collects = ChatRoomResource::class;

    /**
     * Transform the resource collection into an array.
     */
    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
        ];
    }
}
