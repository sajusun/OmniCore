<?php

namespace App\Modules\Interaction\Traits;

use App\Models\User;
use App\Modules\Interaction\Models\Comment;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasComments
{
    /**
     * Get all top-level comments for this model.
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')
            ->whereNull('parent_id')
            ->latest();
    }

    /**
     * Get all comments (including replies) for this model.
     */
    public function allComments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')->latest();
    }

    /**
     * Add a comment (or reply) to this model.
     */
    public function addComment(string $body, User|int $user, ?int $parentId = null): Comment
    {
        $userId = $user instanceof User ? $user->id : $user;

        $comment = $this->allComments()->create([
            'user_id' => $userId,
            'body' => $body,
            'parent_id' => $parentId,
            'status' => 'approved',
        ]);

        if ($parentId) {
            Comment::where('id', $parentId)->increment('replies_count');
        }

        return $comment;
    }

    /**
     * Get total count of top-level comments.
     */
    public function commentsCount(): int
    {
        return $this->comments()->count();
    }
}
