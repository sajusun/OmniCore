<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostComment extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Parent Comment
    public function parent()
    {
        return $this->belongsTo(PostComment::class, 'parent_id');
    }

    // Replies
    public function replies()
    {
        return $this->hasMany(PostComment::class, 'parent_id');
    }
    // public function replies()
    // {
    //     return $this->hasMany(PostComment::class, 'parent_id')
    //         ->with([
    //             'user',
    //             'likes',
    //             'replies',
    //         ]);
    // }

    public function likes()
    {
        return $this->hasMany(PostCommentLike::class, 'comment_id');
    }
}
