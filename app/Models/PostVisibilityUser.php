<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostVisibilityUser extends Model
{
    protected $fillable = [
        'post_id',
        'user_id',
    ];

    /**
     * Post
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Allowed User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}