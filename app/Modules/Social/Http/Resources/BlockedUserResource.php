<?php

namespace App\Modules\Social\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlockedUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name ?? null,
            'username' => $this->username ?? null,
            'avatar' => !empty($this->avatar) ? url($this->avatar) : null,
            'blocked_at' => $this->when($this->pivot, $this->pivot?->created_at?->diffForHumans()),
        ];
    }
}
