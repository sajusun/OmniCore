<?php

namespace App\Modules\Payment\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'payment_id' => $this->payment_id,
            'user_id' => $this->user_id,
            'payable_type' => $this->payable_type ? class_basename($this->payable_type) : null,
            'payable_id' => $this->payable_id,
            'amount' => (float) $this->amount,
            'fee' => (float) $this->fee,
            'total_amount' => (float) $this->total_amount,
            'currency' => $this->currency,
            'method' => $this->method?->value ?? $this->method,
            'status' => $this->status?->value ?? $this->status,
            'gateway_transaction_id' => $this->gateway_transaction_id,
            'payment_url' => $this->payment_url,
            'failure_reason' => $this->failure_reason,
            'paid_at' => $this->paid_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
