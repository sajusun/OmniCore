<?php

namespace App\Modules\Social\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    protected $fillable = [
        'user_id',
        'friend_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function friend()
    {
        return $this->belongsTo(User::class, 'friend_id');
    }
}
