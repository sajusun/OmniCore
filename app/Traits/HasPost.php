<?php

namespace App\Traits;

use App\Models\Post;
use App\Models\PostLike;
use App\Models\PostView;
use App\Models\SavedPost;
use App\Models\PostComment;
use App\Models\PostCommentLike;

trait HasPost
{
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function postLikes()
    {
        return $this->hasMany(PostLike::class);
    }

    public function comments()
    {
        return $this->hasMany(PostComment::class);
    }

    public function commentLikes()
    {
        return $this->hasMany(PostCommentLike::class);
    }

    // public function savedPosts()
    // {
    //     return $this->hasMany(SavedPost::class);
    // }
      public function savedPosts()
    {
        return $this->belongsToMany(
            Post::class,
            'saved_posts',
            'user_id',
            'post_id'
        )->withTimestamps();
    }

    public function postViews()
    {
        return $this->hasMany(PostView::class);
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