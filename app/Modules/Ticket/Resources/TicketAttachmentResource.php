<?php

declare(strict_types=1);

namespace App\Modules\Ticket\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketAttachmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'file_name' => $this->file_name,
            'file_url'  => $this->url,
            'file_size' => (int) $this->file_size,
            'file_type' => $this->file_type,
        ];
    }
}
