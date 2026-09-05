<?php

namespace App\Modules\Product\Traits;

use App\Modules\Interaction\Models\Bookmark;
use App\Modules\Order\Models\Order;
use App\Modules\Order\Models\UserAddress;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\ProductReview;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasEcommerce
{
    public function addresses(): HasMany
    {
        return $this->hasMany(UserAddress::class, 'user_id');
    }

    public function defaultAddress(): HasOne
    {
        return $this->hasOne(UserAddress::class, 'user_id')->where('is_default', true);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id')->latest();
    }

    public function productReviews(): HasMany
    {
        return $this->hasMany(ProductReview::class, 'user_id');
    }

    public function hasInWishlist(int $productId): bool
    {
        return Bookmark::where('user_id', $this->id)
            ->where('bookmarkable_type', 'product')
            ->where('bookmarkable_id', $productId)
            ->where('collection', 'wishlist')
            ->exists();
    }

    public function toggleWishlist(int $productId): array
    {
        $existing = Bookmark::where('user_id', $this->id)
            ->where('bookmarkable_type', 'product')
            ->where('bookmarkable_id', $productId)
            ->where('collection', 'wishlist')
            ->first();

        if ($existing) {
            $existing->delete();
            return [
                'action' => 'removed',
                'in_wishlist' => false,
                'message' => 'Product removed from your wishlist.',
            ];
        }

        Bookmark::create([
            'user_id' => $this->id,
            'bookmarkable_type' => 'product',
            'bookmarkable_id' => $productId,
            'collection' => 'wishlist',
        ]);

        return [
            'action' => 'added',
            'in_wishlist' => true,
            'message' => 'Product added to your wishlist.',
        ];
    }
}
