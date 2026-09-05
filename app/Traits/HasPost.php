<?php

namespace App\Traits;

use App\Modules\Post\Models\Post;

trait HasPost
{
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function visiblePosts()
    {
        return $this->belongsToMany(
            Post::class,
            'post_visibility_users',
            'user_id',
            'post_id'
        )->withTimestamps();
    }
}