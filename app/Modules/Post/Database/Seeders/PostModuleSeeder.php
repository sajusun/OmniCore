<?php

namespace App\Modules\Post\Database\Seeders;

use App\Models\User;
use App\Modules\Post\Models\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostModuleSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::take(5)->get();

        if ($users->isEmpty()) {
            return;
        }

        $samplePosts = [
            [
                'content'    => '🚀 Just launched the brand new OmniCore architecture! Super excited about the scalable, modular foundation and clean API standards.',
                'visibility' => 'public',
                'tags'       => ['tech', 'laravel', 'release'],
            ],
            [
                'content'    => 'Exploring modern microservices & event-driven WebSockets with Laravel Reverb. Real-time notifications and chat are lightning fast! ⚡',
                'visibility' => 'public',
                'tags'       => ['websockets', 'chat', 'realtime'],
            ],
            [
                'content'    => 'Design tips: Always build single-source-of-truth API envelopes and auto-detect pagination formats to keep client apps happy.',
                'visibility' => 'public',
                'tags'       => ['design', 'api', 'architecture'],
            ],
            [
                'content'    => 'Weekend project update: E-Commerce multi-facet catalog with variable products, SKU tracking, and atomic carts. What are you building today?',
                'visibility' => 'public',
                'tags'       => ['ecommerce', 'devlife', 'weekend'],
            ],
            [
                'content'    => 'Universal polymorphic interactions (Likes, Comments, Bookmarks, Views) make scaling social and product features completely frictionless. 🔥',
                'visibility' => 'public',
                'tags'       => ['laravel', 'patterns', 'database'],
            ],
        ];

        foreach ($samplePosts as $postData) {
            $user = $users->random();
            $post = Post::create([
                'user_id'    => $user->id,
                'content'    => $postData['content'],
                'visibility' => $postData['visibility'],
                'status'     => 'published',
                'share_link' => Str::random(24),
            ]);

            // Add sample likes and comments from other users
            foreach ($users as $otherUser) {
                if ($otherUser->id !== $user->id) {
                    if (rand(0, 1) === 1) {
                        $post->toggleLike($otherUser);
                    }
                    if (rand(0, 1) === 1) {
                        $post->recordView($otherUser, '127.0.0.1');
                    }
                }
            }

            // Add sample comments
            $post->addComment('Great work on this! Looking forward to seeing more.', $users->random());
        }

        echo "✓ Post Module Seeding Complete!\n";
    }
}
