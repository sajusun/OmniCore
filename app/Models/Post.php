<?php

namespace App\Models;

use App\Enums\PostType;
use Illuminate\Support\Str;
use App\Modules\Media\Traits\HasMedia;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasMedia;
    protected $guarded = [];

    protected $appends = [
        'short_description'
    ];

    protected function casts(): array
    {
        return [
            'type' => PostType::class,
        ];
    }


    protected static function booted(): void
    {
        static::creating(function (Post $post) {

            if (empty($post->share_link)) {
                $post->share_link = Str::ulid()->toBase32();
            }
        });
    }

    public function getShortDescriptionAttribute()
    {
        $shortDescription = strip_tags($this->content);

        return Str::length($shortDescription) > 200 ? Str::substr($shortDescription, 0, 200) . '...' : $shortDescription;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function likes()
    {
        return $this->hasMany(PostLike::class);
    }

    public function likedUsers()
    {
        return $this->belongsToMany(
            User::class,
            'post_likes',
            'post_id',
            'user_id'
        )->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(PostComment::class)->whereNull('parent_id');
    }

    public function allComments()
    {
        return $this->hasMany(PostComment::class);
    }

    public function saves()
    {
        return $this->hasMany(SavedPost::class);
    }

    public function savedPosts()
    {
        return $this->belongsToMany(
            Post::class,
            'saved_posts',
            'user_id',
            'post_id'
        )->withTimestamps();
    }

    public function views()
    {
        return $this->hasMany(PostView::class);
    }

    //   Share Post 
    public function sharedPost()
    {
        return $this->belongsTo(Post::class, 'shared_post_id');
    }

    // Original Post will be Share
    public function shares()
    {
        return $this->hasMany(Post::class, 'shared_post_id');
    }

    public function visibleUsers()
    {
        return $this->belongsToMany(
            User::class,
            'post_visibility_users',
            'post_id',
            'user_id'
        )->withTimestamps();
    }
}
