<?php

declare(strict_types=1);

namespace App\Modules\Affiliate\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AffiliateCommissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'order_amount'      => (float) $this->order_amount,
            'commission_rate'   => (float) $this->commission_rate,
            'commission_amount' => (float) $this->commission_amount,
            'status'            => $this->status,
            'approved_at'       => $this->approved_at?->toIso8601String(),
            'paid_at'           => $this->paid_at?->toIso8601String(),
            'created_at'        => $this->created_at?->toIso8601String(),
        ];
    }
}
