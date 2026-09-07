<?php

namespace App\Modules\Product\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Product\Http\Resources\ProductListResource;
use App\Modules\Product\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get customer wishlist items
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $products = $user->wishlistProducts()
            ->with(['category', 'brand', 'media'])
            ->published()
            ->latest('products.created_at')
            ->paginate((int) $request->get('per_page', 20));

        return $this->paginated(
            $products,
            ProductListResource::class,
            'Wishlist items fetched successfully.'
        );
    }

    /**
     * Toggle item in wishlist (Add / Remove)
     */
    public function toggle(int $productId, Request $request): JsonResponse
    {
        $product = Product::findOrFail($productId);
        $user = $request->user();

        $result = $user->toggleWishlist($product->id);

        return $this->success([
            'action' => $result['action'],
            'in_wishlist' => $result['in_wishlist'],
        ], $result['message']);
    }
}
