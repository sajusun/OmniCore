<?php

declare(strict_types=1);

namespace App\Modules\Ticket\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'description' => $this->description,
            'icon'        => $this->icon,
            'is_active'   => (bool) $this->is_active,
            'sort_order'  => (int) $this->sort_order,
        ];
    }
}
