<?php

namespace App\Modules\Social\Models;

use App\Models\User;
use App\Modules\Social\Enums\FriendRequestStatus;
use Illuminate\Database\Eloquent\Model;

class FriendRequest extends Model
{
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'status',
        'accepted_at',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'status' => FriendRequestStatus::class,
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
