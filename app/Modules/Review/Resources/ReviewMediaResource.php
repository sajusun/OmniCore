<?php

declare(strict_types=1);

namespace App\Modules\Review\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewMediaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'file_url'   => $this->url,
            'file_type'  => $this->file_type,
            'file_size'  => (int) $this->file_size,
            'sort_order' => (int) $this->sort_order,
        ];
    }
}
