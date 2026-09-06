<?php

declare(strict_types=1);

namespace App\Modules\Vendor\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorPayoutResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'amount'         => (float) $this->amount,
            'status'         => $this->status->value ?? $this->status,
            'status_label'   => method_exists($this->status, 'label') ? $this->status->label() : ucfirst($this->status),
            'payout_method'  => $this->payout_method,
            'transaction_id' => $this->transaction_id,
            'processed_at'   => $this->processed_at?->toIso8601String(),
            'created_at'     => $this->created_at?->toIso8601String(),
        ];
    }
}
