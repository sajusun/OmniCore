<?php

declare(strict_types=1);

namespace App\Modules\Review\Database\Seeders;

use App\Models\User;
use App\Modules\Product\Models\Product;
use App\Modules\Review\Enums\ReviewStatus;
use App\Modules\Review\Models\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $product = Product::first();

        if ($user && $product) {
            Review::firstOrCreate(
                [
                    'user_id'         => $user->id,
                    'reviewable_type' => Product::class,
                    'reviewable_id'   => $product->id,
                ],
                [
                    'rating'            => 5,
                    'title'             => 'Outstanding build quality and fast shipping!',
                    'comment'           => 'This item exceeded my expectations. The finish is premium, packaging was super secure, and delivery took less than 48 hours.',
                    'criteria_ratings'  => [
                        'quality'  => 5,
                        'value'    => 5,
                        'delivery' => 5,
                    ],
                    'is_verified_buyer' => true,
                    'status'            => ReviewStatus::APPROVED->value,
                    'helpful_count'     => 12,
                    'unhelpful_count'   => 0,
                    'vendor_reply'      => 'Thank you so much for your wonderful feedback! We are thrilled that you love the product.',
                    'vendor_replied_at' => now(),
                ]
            );
        }
    }
}
