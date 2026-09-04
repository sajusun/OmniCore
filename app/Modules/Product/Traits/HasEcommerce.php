<?php

namespace App\Modules\Product\Traits;

use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\ProductReview;
use App\Modules\Product\Models\ProductWishlist;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasEcommerce
{
    public function productWishlists(): HasMany
    {
        return $this->hasMany(ProductWishlist::class, 'user_id');
    }

    public function wishlistProducts(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'product_wishlists',
            'user_id',
            'product_id'
        )->withTimestamps();
    }

    public function productReviews(): HasMany
    {
        return $this->hasMany(ProductReview::class, 'user_id');
    }

    public function hasInWishlist(int $productId): bool
    {
        return $this->productWishlists()->where('product_id', $productId)->exists();
    }

    public function toggleWishlist(int $productId): array
    {
        $existing = $this->productWishlists()->where('product_id', $productId)->first();

        if ($existing) {
            $existing->delete();
            return [
                'action' => 'removed',
                'in_wishlist' => false,
                'message' => 'Product removed from your wishlist.',
            ];
        }

        $this->productWishlists()->create(['product_id' => $productId]);

        return [
            'action' => 'added',
            'in_wishlist' => true,
            'message' => 'Product added to your wishlist.',
        ];
    }
}
