<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->when($this->first_name && $this->first_name !== 'null', $this->first_name),
            'last_name' => $this->when($this->last_name && $this->last_name !== 'null', $this->last_name),
            'phone' => $this->when($this->phone && $this->phone !== 'null', $this->phone),
            'gender' => $this->when($this->gender && $this->gender !== 'null', $this->gender),
            'address' => $this->when($this->address && $this->address !== 'null', $this->address),
            'country' => $this->when($this->country && $this->country !== 'null', $this->country),
            'state' => $this->when($this->state && $this->state !== 'null', $this->state),
            'city' => $this->when($this->city && $this->city !== 'null', $this->city),
            'zip_code' => $this->when($this->zip_code && $this->zip_code !== 'null', $this->zip_code),
            'latitude' => $this->when($this->latitude && $this->latitude !== 'null', $this->latitude),
            'longitude' => $this->when($this->longitude && $this->longitude !== 'null', $this->longitude),

        ];
    }
}
