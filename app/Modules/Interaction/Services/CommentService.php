<?php

namespace App\Modules\Interaction\Services;

use App\Models\User;
use App\Modules\Interaction\Models\Comment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use InvalidArgumentException;

class CommentService
{
    /**
     * Resolve target model from type and ID.
     */
    public function resolveModel(string $type, int|string $id): Model
    {
        $morphMap = Relation::morphMap();
        $modelClass = $morphMap[$type] ?? (class_exists($type) ? $type : null);

        if (!$modelClass || !class_exists($modelClass)) {
            throw new InvalidArgumentException("Invalid commentable type: {$type}");
        }

        $model = $modelClass::find($id);
        if (!$model) {
            throw new InvalidArgumentException("Target model not found for {$type} #{$id}");
        }

        return $model;
    }

    /**
     * Get paginated comments with replies and user relation for a target model.
     */
    public function getCommentsForModel(Model $model, int $perPage = 15): LengthAwarePaginator
    {
        return Comment::where('commentable_type', $model->getMorphClass())
            ->where('commentable_id', $model->getKey())
            ->whereNull('parent_id')
            ->with([
                'user:id,name,avatar,email',
                'replies' => function ($query) {
                    $query->with('user:id,name,avatar,email')->latest();
                }
            ])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Create a new top-level comment.
     */
    public function createComment(Model $model, User|int $user, string $body): Comment
    {
        $userId = $user instanceof User ? $user->id : $user;

        return Comment::create([
            'user_id' => $userId,
            'commentable_type' => $model->getMorphClass(),
            'commentable_id' => $model->getKey(),
            'parent_id' => null,
            'body' => $body,
            'status' => 'approved',
        ]);
    }

    /**
     * Create a reply under an existing comment.
     */
    public function createReply(Comment $parentComment, User|int $user, string $body): Comment
    {
        $userId = $user instanceof User ? $user->id : $user;

        $reply = Comment::create([
            'user_id' => $userId,
            'commentable_type' => $parentComment->commentable_type,
            'commentable_id' => $parentComment->commentable_id,
            'parent_id' => $parentComment->id,
            'body' => $body,
            'status' => 'approved',
        ]);

        $parentComment->increment('replies_count');

        return $reply;
    }

    /**
     * Delete a comment (or reply).
     */
    public function deleteComment(Comment $comment, User|int $user): bool
    {
        $userId = $user instanceof User ? $user->id : $user;

        if ($comment->user_id !== $userId) {
            throw new InvalidArgumentException("Unauthorized to delete this comment.");
        }

        if ($comment->parent_id) {
            Comment::where('id', $comment->parent_id)->where('replies_count', '>', 0)->decrement('replies_count');
        }

        return (bool) $comment->delete();
    }
}
