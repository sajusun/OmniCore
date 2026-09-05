<?php

namespace App\Modules\Post\Models;

use App\Enums\PostType;
use App\Models\User;
use App\Modules\Interaction\Traits\HasInteractions;
use App\Modules\Media\Traits\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasMedia, HasInteractions;

    protected $guarded = [];

    protected $appends = [
        'short_description',
    ];

    protected function casts(): array
    {
        return [
            'type' => PostType::class,
        ];
    }

    public function scopeFilterByUser($query, $userId = null)
    {
        return $query->when($userId, function ($q, $userId) {
            return $q->where('user_id', $userId);
        });
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

    // Share Post (referenced original)
    public function sharedPost()
    {
        return $this->belongsTo(Post::class, 'shared_post_id');
    }

    // Shares of this post
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
