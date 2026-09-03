<?php

namespace App\Modules\Chat\Http\Resources;

use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ReadReceiptResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'user'    => new UserResource($this->user),
            'read_at' => $this->last_read_at?->toIso8601String(),
        ];
    }
}
