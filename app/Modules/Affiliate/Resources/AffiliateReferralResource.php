<?php

declare(strict_types=1);

namespace App\Modules\Affiliate\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AffiliateReferralResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'campaign'     => $this->campaign,
            'landing_page' => $this->landing_page,
            'status'       => $this->status->value ?? $this->status,
            'status_label' => method_exists($this->status, 'label') ? $this->status->label() : ucfirst($this->status),
            'referred_user'=> $this->whenLoaded('referredUser', function () {
                return $this->referredUser ? [
                    'id'   => $this->referredUser->id,
                    'name' => $this->referredUser->name,
                ] : null;
            }),
            'converted_at' => $this->converted_at?->toIso8601String(),
            'created_at'   => $this->created_at?->toIso8601String(),
        ];
    }
}
