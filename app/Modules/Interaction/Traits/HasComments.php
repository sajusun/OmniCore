<?php

namespace App\Modules\Interaction\Traits;

use App\Models\User;
use App\Modules\Interaction\Models\Comment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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
            ->latest('id');
    }

    /**
     * Get all comments (including replies) for this model.
     */
    public function allComments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')->latest('id');
    }

    /**
     * Get paginated comments with user and replies eagerly loaded.
     */
    public function getComments(int $perPage = 15): LengthAwarePaginator
    {
        return $this->comments()
            ->with([
                'user:id,name,avatar,email',
                'replies' => function ($query) {
                    $query->with('user:id,name,avatar,email')->latest('id');
                },
            ])
            ->paginate($perPage);
    }

    /**
     * Add a comment (or reply) to this model.
     */
    public function addComment(string $body, User|int $user, ?int $parentId = null, string $status = 'approved'): Comment
    {
        $userId = $user instanceof User ? $user->id : $user;

        $comment = $this->allComments()->create([
            'user_id' => $userId,
            'body' => $body,
            'parent_id' => $parentId,
            'status' => $status,
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

    /**
     * Get total count of all comments including nested replies.
     */
    public function totalCommentsCount(): int
    {
        return $this->allComments()->count();
    }
}
