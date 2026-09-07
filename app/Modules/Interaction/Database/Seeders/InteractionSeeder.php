<?php

namespace App\Modules\Interaction\Database\Seeders;

use App\Models\User;
use App\Modules\Interaction\Services\BookmarkService;
use App\Modules\Interaction\Services\CommentService;
use App\Modules\Interaction\Services\LikeService;
use App\Modules\Interaction\Services\ViewService;
use App\Modules\Post\Models\Post;
use App\Modules\Product\Models\Product;
use Illuminate\Database\Seeder;

class InteractionSeeder extends Seeder
{
    /**
     * Run interaction seeders.
     */
    public function run(): void
    {
        $users = User::take(10)->get();
        if ($users->isEmpty()) {
            return;
        }

        $likeService = app(LikeService::class);
        $commentService = app(CommentService::class);
        $bookmarkService = app(BookmarkService::class);
        $viewService = app(ViewService::class);

        // 1. Seed Interactions for Products
        $products = Product::take(8)->get();
        foreach ($products as $product) {
            foreach ($users as $index => $user) {
                // Likes with varied reactions
                if ($index % 2 === 0) {
                    $likeService->like($product, $user, $index % 4 === 0 ? 'heart' : 'like');
                }

                // Bookmarks in wishlist and default collections
                if ($index % 3 === 0) {
                    $bookmarkService->bookmark($product, $user, 'wishlist');
                }

                // Views
                $viewService->recordView($product, $user, '127.0.0.'.($index + 1), 0);
            }

            // Seed some comments
            $sampleComments = [
                'Amazing build quality and fast shipping! Highly recommended.',
                'Is this compatible with the latest version?',
                'Great value for money. Loving the design.',
                'The product matches the description perfectly.',
            ];

            foreach (array_slice($sampleComments, 0, rand(2, 4)) as $commentIndex => $body) {
                $commentUser = $users[$commentIndex % $users->count()];
                $comment = $commentService->createComment($product, $commentUser, $body);

                // Add a reply
                if ($commentIndex === 0) {
                    $replyUser = $users[($commentIndex + 1) % $users->count()];
                    $commentService->createReply($comment, $replyUser, 'Thanks for your feedback! Glad you liked it.');
                }
            }
        }

        // 2. Seed Interactions for Posts
        $posts = Post::take(8)->get();
        foreach ($posts as $post) {
            foreach ($users as $index => $user) {
                if ($index % 2 === 1) {
                    $likeService->like($post, $user, 'like');
                }

                if ($index % 4 === 0) {
                    $bookmarkService->bookmark($post, $user, 'saved');
                }

                $viewService->recordView($post, $user, '192.168.1.'.($index + 1), 0);
            }

            $postComments = [
                'Great insights, looking forward to the next update!',
                'Very interesting perspective on modern architecture.',
                'Clean implementation and great write-up.',
            ];

            foreach ($postComments as $cIdx => $pBody) {
                $cUser = $users[$cIdx % $users->count()];
                $postComment = $commentService->createComment($post, $cUser, $pBody);

                if ($cIdx === 1) {
                    $rUser = $users[($cIdx + 1) % $users->count()];
                    $commentService->createReply($postComment, $rUser, 'Totally agree with this point.');
                }
            }
        }
    }
}
