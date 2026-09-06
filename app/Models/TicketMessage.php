<?php

namespace App\Models;

use App\Modules\Ticket\Models\TicketReply;

class TicketMessage extends TicketReply
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
