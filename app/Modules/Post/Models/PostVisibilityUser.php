<?php

namespace App\Modules\Post\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PostVisibilityUser extends Model
{
    protected $fillable = [
        'post_id',
        'user_id',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
