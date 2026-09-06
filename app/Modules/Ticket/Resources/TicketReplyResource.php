<?php

declare(strict_types=1);

namespace App\Modules\Ticket\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketReplyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'ticket_id'        => $this->ticket_id,
            'user'             => [
                'id'         => $this->user?->id,
                'name'       => $this->user?->name,
                'avatar_url' => $this->user?->avatar_url,
                'is_staff'   => $this->user?->hasRole(['Super Admin', 'Admin', 'Staff']) ?? false,
            ],
            'is_internal_note' => (bool) $this->is_internal_note,
            'message'          => $this->message,
            'attachments'      => TicketAttachmentResource::collection($this->whenLoaded('attachments')),
            'created_at'       => $this->created_at?->toIso8601String(),
        ];
    }
}
