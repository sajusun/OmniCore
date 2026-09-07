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

        if (! $modelClass || ! class_exists($modelClass)) {
            throw new InvalidArgumentException("Invalid commentable type: {$type}");
        }

        $model = $modelClass::find($id);
        if (! $model) {
            throw new InvalidArgumentException("Target model not found for {$type} #{$id}");
        }

        return $model;
    }

    /**
     * Get paginated comments with replies and user relation for a target model.
     */
    public function getCommentsForModel(Model $model, int $perPage = 15, ?string $status = null): LengthAwarePaginator
    {
        $query = Comment::where('commentable_type', $model->getMorphClass())
            ->where('commentable_id', $model->getKey())
            ->whereNull('parent_id')
            ->with([
                'user:id,name,avatar,email',
                'replies' => function ($query) use ($status) {
                    $query->with('user:id,name,avatar,email')->latest('id');
                    if ($status) {
                        $query->where('status', $status);
                    }
                },
            ])
            ->latest('id');

        if ($status) {
            $query->where('status', $status);
        }

        return $query->paginate($perPage);
    }

    /**
     * Create a new top-level comment.
     */
    public function createComment(Model $model, User|int $user, string $body, string $status = 'approved'): Comment
    {
        $userId = $user instanceof User ? $user->id : $user;

        return Comment::create([
            'user_id' => $userId,
            'commentable_type' => $model->getMorphClass(),
            'commentable_id' => $model->getKey(),
            'parent_id' => null,
            'body' => $body,
            'status' => $status,
        ]);
    }

    /**
     * Create a reply under an existing comment.
     */
    public function createReply(Comment $parentComment, User|int $user, string $body, string $status = 'approved'): Comment
    {
        $userId = $user instanceof User ? $user->id : $user;

        $reply = Comment::create([
            'user_id' => $userId,
            'commentable_type' => $parentComment->commentable_type,
            'commentable_id' => $parentComment->commentable_id,
            'parent_id' => $parentComment->id,
            'body' => $body,
            'status' => $status,
        ]);

        $parentComment->increment('replies_count');

        return $reply;
    }

    /**
     * Delete a comment (or reply) by its author.
     */
    public function deleteComment(Comment $comment, User|int $user): bool
    {
        $userId = $user instanceof User ? $user->id : $user;

        if ($comment->user_id !== $userId) {
            throw new InvalidArgumentException('Unauthorized to delete this comment.');
        }

        return $this->adminDeleteComment($comment);
    }

    /**
     * Delete a comment (or reply) with admin privilege and maintain counters.
     */
    public function adminDeleteComment(Comment $comment): bool
    {
        if ($comment->parent_id) {
            Comment::where('id', $comment->parent_id)
                ->where('replies_count', '>', 0)
                ->decrement('replies_count');
        }

        // Also delete child replies if deleting parent comment
        Comment::where('parent_id', $comment->id)->delete();

        return (bool) $comment->delete();
    }

    /**
     * Update comment moderation status ('approved', 'spam', 'pending', 'rejected').
     */
    public function updateStatus(Comment $comment, string $status): Comment
    {
        $allowed = ['approved', 'spam', 'pending', 'rejected'];
        if (! in_array($status, $allowed)) {
            throw new InvalidArgumentException("Invalid status: {$status}");
        }

        $comment->update(['status' => $status]);

        return $comment;
    }

    /**
     * Get comment counts and status breakdown for a model.
     */
    public function getCommentsStats(Model $model): array
    {
        $countsByStatus = Comment::where('commentable_type', $model->getMorphClass())
            ->where('commentable_id', $model->getKey())
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'total' => array_sum($countsByStatus),
            'approved' => $countsByStatus['approved'] ?? 0,
            'pending' => $countsByStatus['pending'] ?? 0,
            'spam' => $countsByStatus['spam'] ?? 0,
            'rejected' => $countsByStatus['rejected'] ?? 0,
        ];
    }
}
