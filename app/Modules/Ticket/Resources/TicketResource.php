<?php

declare(strict_types=1);

namespace App\Modules\Ticket\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'ticket_number' => $this->ticket_number,
            'subject'       => $this->subject,
            'status'        => $this->status->value ?? $this->status,
            'status_label'  => method_exists($this->status, 'label') ? $this->status->label() : ucfirst($this->status),
            'priority'      => $this->priority->value ?? $this->priority,
            'priority_label'=> method_exists($this->priority, 'label') ? $this->priority->label() : ucfirst($this->priority),
            'category'      => new TicketCategoryResource($this->whenLoaded('category')),
            'assignee'      => $this->whenLoaded('assignee', function () {
                return $this->assignee ? [
                    'id'   => $this->assignee->id,
                    'name' => $this->assignee->name,
                ] : null;
            }),
            'last_reply_at' => $this->last_reply_at?->toIso8601String(),
            'created_at'    => $this->created_at?->toIso8601String(),
            'updated_at'    => $this->updated_at?->toIso8601String(),
        ];
    }
}
