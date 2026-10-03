<?php

declare(strict_types=1);

namespace App\Modules\AppSupport\Events;

use App\Models\User;
use App\Modules\AppSupport\Models\AppSupport;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AppSupportReportCreatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly AppSupport $support,
        public readonly User $user
    ) {}
}
