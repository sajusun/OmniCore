<?php

declare(strict_types=1);

namespace App\Modules\Ticket\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'ticket_number' => $this->ticket_number,
            'subject'       => $this->subject,
            'message'       => $this->message,
            'status'        => $this->status->value ?? $this->status,
            'status_label'  => method_exists($this->status, 'label') ? $this->status->label() : ucfirst($this->status),
            'priority'      => $this->priority->value ?? $this->priority,
            'priority_label'=> method_exists($this->priority, 'label') ? $this->priority->label() : ucfirst($this->priority),
            'user'          => [
                'id'         => $this->user?->id,
                'name'       => $this->user?->name,
                'email'      => $this->user?->email,
                'avatar_url' => $this->user?->avatar_url,
            ],
            'category'      => new TicketCategoryResource($this->whenLoaded('category')),
            'assignee'      => $this->whenLoaded('assignee', function () {
                return $this->assignee ? [
                    'id'   => $this->assignee->id,
                    'name' => $this->assignee->name,
                ] : null;
            }),
            'attachments'   => TicketAttachmentResource::collection($this->whenLoaded('attachments')),
            'replies'       => TicketReplyResource::collection(
                $this->whenLoaded('replies', function () use ($request) {
                    // Filter internal notes if viewer is a regular customer
                    $user = $request->user();
                    $isStaff = $user && $user->hasRole(['Super Admin', 'Admin', 'Staff']);
                    return $this->replies->filter(function ($reply) use ($isStaff) {
                        return $isStaff || !$reply->is_internal_note;
                    });
                })
            ),
            'last_reply_at' => $this->last_reply_at?->toIso8601String(),
            'resolved_at'   => $this->resolved_at?->toIso8601String(),
            'closed_at'     => $this->closed_at?->toIso8601String(),
            'created_at'    => $this->created_at?->toIso8601String(),
            'updated_at'    => $this->updated_at?->toIso8601String(),
        ];
    }
}
