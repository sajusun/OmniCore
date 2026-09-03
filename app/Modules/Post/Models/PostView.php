<?php

namespace App\Modules\Post\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PostView extends Model
{
    protected $guarded = [];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
