<?php
namespace App\Services;

use App\Models\User;
use App\Models\Product;

class ProductService
{
    public function toggleUserProduct(User $user, Product $product, string $source = 'manual'): bool
    {
        $exists = $user->products()
            ->where('product_id', $product->id)
            ->exists();

        if ($exists) {
            $user->products()->detach($product->id);
            return false;
        }

        $user->products()->syncWithoutDetaching([
            $product->id => [
                'source' => $source,
                'added_at' => now(),
            ]
        ]);

        return true;
    }
}