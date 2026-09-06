<?php

declare(strict_types=1);

namespace App\Modules\Affiliate\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AffiliateAccountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'referral_code'        => $this->referral_code,
            'custom_slug'          => $this->custom_slug,
            'referral_url'         => $this->referral_url,
            'commission_rate'      => (float) $this->commission_rate,
            'commission_type'      => $this->commission_type->value ?? $this->commission_type,
            'commission_label'     => method_exists($this->commission_type, 'label') ? $this->commission_type->label() : ucfirst($this->commission_type),
            'status'               => $this->status->value ?? $this->status,
            'status_label'         => method_exists($this->status, 'label') ? $this->status->label() : ucfirst($this->status),
            'total_earnings'       => (float) $this->total_earnings,
            'paid_earnings'        => (float) $this->paid_earnings,
            'current_balance'      => (float) $this->current_balance,
            'lifetime_referrals'   => (int) $this->lifetime_referrals,
            'lifetime_conversions' => (int) $this->lifetime_conversions,
            'conversion_rate'      => $this->lifetime_referrals > 0 ? round(($this->lifetime_conversions / $this->lifetime_referrals) * 100, 1) . '%' : '0%',
        ];
    }
}
