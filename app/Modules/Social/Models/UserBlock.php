<?php

namespace App\Modules\Social\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserBlock extends Model
{
    protected $fillable = [
        'user_id',
        'blocked_user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function blockedUser()
    {
        return $this->belongsTo(User::class, 'blocked_user_id');
    }
}
