<?php

declare(strict_types=1);

namespace App\Modules\Ticket\Traits;

use App\Modules\Ticket\Models\Ticket;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasTickets
{
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'user_id');
    }

    public function assignedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'assigned_to');
    }
}
