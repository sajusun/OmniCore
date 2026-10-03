<?php

declare(strict_types=1);

namespace App\Modules\Ticket\Events;

use App\Modules\Ticket\Models\Ticket;
use App\Modules\Ticket\Models\TicketReply;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketRepliedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Ticket $ticket,
        public readonly TicketReply $reply,
        public readonly bool $isStaffReply
    ) {}
}
