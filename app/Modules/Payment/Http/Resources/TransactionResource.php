<?php

namespace App\Modules\Payment\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'trx_id' => $this->trx_id,
            'type' => $this->type?->value ?? $this->type,
            'amount' => (float) $this->amount,
            'balance_before' => (float) $this->balance_before,
            'balance_after' => (float) $this->balance_after,
            'fee' => (float) $this->fee,
            'currency' => $this->currency,
            'description' => $this->description,
            'reference_type' => $this->reference_type ? class_basename($this->reference_type) : null,
            'reference_id' => $this->reference_id,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
