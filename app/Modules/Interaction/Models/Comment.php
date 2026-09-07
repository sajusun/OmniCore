<?php

namespace App\Modules\Interaction\Models;

use App\Models\User;
use App\Modules\Interaction\Traits\HasLikes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasLikes, SoftDeletes;

    protected $table = 'comments';

    protected $fillable = [
        'user_id',
        'commentable_type',
        'commentable_id',
        'parent_id',
        'body',
        'likes_count',
        'replies_count',
        'status',
    ];

    protected $casts = [
        'likes_count' => 'integer',
        'replies_count' => 'integer',
    ];

    /**
     * The model that this comment belongs to (Post, Product, etc.).
     */
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * The author of this comment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The parent comment (if this is a reply).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Replies belonging to this comment.
     *
     * @return HasMany<Comment, $this>
     */
    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Check if this comment is a reply.
     */
    public function isReply(): bool
    {
        return ! is_null($this->parent_id);
    }

    /**
     * Add a reply to this comment.
     */
    public function reply(string $body, User|int $user): self
    {
        $userId = $user instanceof User ? $user->id : $user;

        $reply = self::create([
            'user_id' => $userId,
            'commentable_type' => $this->commentable_type,
            'commentable_id' => $this->commentable_id,
            'parent_id' => $this->id,
            'body' => $body,
            'status' => 'approved',
        ]);

        $this->increment('replies_count');

        return $reply;
    }
}
