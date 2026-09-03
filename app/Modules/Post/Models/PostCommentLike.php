<?php

namespace App\Modules\Post\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PostCommentLike extends Model
{
    protected $guarded = [];

    public function comment()
    {
        return $this->belongsTo(PostComment::class, 'comment_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
