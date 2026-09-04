<?php

namespace App\Modules\Order\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'recipient_name' => $this->recipient_name,
            'phone' => $this->phone,
            'street_address' => $this->street_address,
            'apartment_suite' => $this->apartment_suite,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'is_default' => (bool) $this->is_default,
            'formatted_address' => $this->formatted_address,
        ];
    }
}
