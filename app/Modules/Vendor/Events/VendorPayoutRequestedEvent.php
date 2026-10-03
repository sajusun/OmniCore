<?php

declare(strict_types=1);

namespace App\Modules\Vendor\Events;

use App\Modules\Vendor\Models\VendorPayout;
use App\Modules\Vendor\Models\VendorStore;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VendorPayoutRequestedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly VendorStore $store,
        public readonly VendorPayout $payout,
        public readonly bool $isWallet
    ) {}
}
