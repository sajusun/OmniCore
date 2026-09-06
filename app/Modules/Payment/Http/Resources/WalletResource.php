<?php

namespace App\Modules\Payment\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'balance' => (float) $this->balance,
            'formatted_balance' => number_format((float) $this->balance, 2) . ' ' . $this->currency,
            'currency' => $this->currency,
            'is_active' => (bool) $this->is_active,
            'is_frozen' => (bool) $this->is_frozen,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
