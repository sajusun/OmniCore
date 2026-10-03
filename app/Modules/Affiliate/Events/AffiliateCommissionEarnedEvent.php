<?php

declare(strict_types=1);

namespace App\Modules\Affiliate\Events;

use App\Modules\Affiliate\Models\AffiliateAccount;
use App\Modules\Affiliate\Models\AffiliateCommission;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AffiliateCommissionEarnedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly AffiliateAccount $account,
        public readonly AffiliateCommission $commission,
        public readonly float $orderAmount,
        public readonly float $commissionAmount
    ) {}
}
