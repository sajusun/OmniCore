<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    protected $fillable = ['email', 'ip_address', 'user_agent'];

    // Auto cast timestamp to Carbon
    protected $casts = [
        'subscribed_at' => 'datetime',
    ];
}
