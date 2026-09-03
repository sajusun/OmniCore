<?php

namespace App\Modules\Social\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RelationshipStatusResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'type'                 => $this->resource['type'] ?? 'none',
            'is_self'              => $this->resource['is_self'] ?? false,
            'is_friend'            => $this->resource['is_friend'] ?? false,
            'is_following'         => $this->resource['is_following'] ?? false,
            'is_followed_by'       => $this->resource['is_followed_by'] ?? false,
            'has_blocked'          => $this->resource['has_blocked'] ?? false,
            'is_blocked_by'        => $this->resource['is_blocked_by'] ?? false,
            'friend_request'       => $this->resource['friend_request'] ?? null,
            'mutual_friends_count' => $this->resource['mutual_friends_count'] ?? 0,
        ];
    }
}
