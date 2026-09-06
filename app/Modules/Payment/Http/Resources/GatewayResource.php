<?php

namespace App\Modules\Payment\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GatewayResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'logo' => $this->logo,
            'is_active' => (bool) $this->is_active,
            'is_sandbox' => (bool) $this->is_sandbox,
            'currencies' => $this->currencies ?? [],
            'fee_fixed' => (float) $this->fee_fixed,
            'fee_percent' => (float) $this->fee_percent,
        ];
    }
}
