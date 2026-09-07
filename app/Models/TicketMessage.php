<?php

namespace App\Models;

use App\Modules\Ticket\Models\TicketReply;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketMessage extends TicketReply
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
