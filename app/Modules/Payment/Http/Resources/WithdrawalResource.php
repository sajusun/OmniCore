<?php

namespace App\Modules\Payment\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WithdrawalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => (float) $this->amount,
            'fee' => (float) $this->fee,
            'payable_amount' => (float) $this->payable_amount,
            'currency' => $this->currency,
            'method' => $this->method,
            'account_details' => $this->account_details,
            'status' => $this->status?->value ?? $this->status,
            'admin_note' => $this->admin_note,
            'rejection_reason' => $this->rejection_reason,
            'processed_at' => $this->processed_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
