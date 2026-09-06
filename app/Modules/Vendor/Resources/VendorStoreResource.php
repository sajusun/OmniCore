<?php

declare(strict_types=1);

namespace App\Modules\Vendor\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorStoreResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'slug'            => $this->slug,
            'description'     => $this->description,
            'logo_url'        => $this->logo_url,
            'banner_url'      => $this->banner_url,
            'phone'           => $this->phone,
            'email'           => $this->email,
            'address'         => $this->address,
            'commission_rate' => (float) $this->commission_rate,
            'status'          => $this->status->value ?? $this->status,
            'status_label'    => method_exists($this->status, 'label') ? $this->status->label() : ucfirst($this->status),
            'total_sales'     => (float) $this->total_sales,
            'total_earnings'  => (float) $this->total_earnings,
            'balance'         => (float) $this->balance,
            'is_featured'     => (bool) $this->is_featured,
            'average_rating'  => $this->average_rating,
            'reviews_count'   => $this->reviews_count,
            'created_at'      => $this->created_at?->toIso8601String(),
        ];
    }
}
