<?php

namespace App\Modules\Post\Services;

use App\Modules\Interaction\Models\Comment;
use App\Modules\Post\Models\Post;
use Illuminate\Support\Facades\Auth;

class PostCommentService
{
    public function index(Post $post)
    {
        return $post->comments()
            ->with(['user', 'likes', 'replies.user', 'replies.likes'])
            ->withCount([
                'likes',
                'replies',
            ])
            ->latest()
            ->get();
    }

    public function store(Post $post, array $data): Comment
    {
        $userId = Auth::id() ?? auth('api')->id();

        return $post->addComment($data['comment'] ?? $data['body'] ?? '', $userId)->load('user');
    }

    public function update(Comment $comment, array $data): Comment
    {
        $comment->update([
            'body' => $data['comment'] ?? $data['body'] ?? $comment->body,
        ]);

        return $comment->fresh()->load('user');
    }

    public function destroy(Comment $comment): bool
    {
        return (bool) $comment->delete();
    }

    public function reply(Comment $comment, array $data): Comment
    {
        $userId = Auth::id() ?? auth('api')->id();

        return $comment->reply($data['comment'] ?? $data['body'] ?? '', $userId)->load('user');
    }

    public function toggleLike(Comment $comment): bool
    {
        $userId = Auth::id() ?? auth('api')->id();
        $res = $comment->toggleLike($userId);

        return (bool) ($res['liked'] ?? false);
    }

    public function replies(Comment $comment)
    {
        return $comment->replies()
            ->with(['user', 'likes', 'replies.user', 'replies.likes'])
            ->withCount([
                'likes',
                'replies',
            ])
            ->latest()
            ->paginate(10);
    }
}
