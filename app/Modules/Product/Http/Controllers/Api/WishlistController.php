<?php

namespace App\Modules\Product\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Product\Http\Resources\ProductListResource;
use App\Modules\Product\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * Get customer wishlist items
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $products = $user->wishlistProducts()
            ->with(['category', 'brand', 'media'])
            ->published()
            ->latest('product_wishlists.created_at')
            ->paginate((int) $request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => ProductListResource::collection($products),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    /**
     * Toggle item in wishlist (Add / Remove)
     */
    public function toggle(int $productId, Request $request): JsonResponse
    {
        $product = Product::findOrFail($productId);
        $user = $request->user();

        $result = $user->toggleWishlist($product->id);

        return response()->json([
            'success' => true,
            'action' => $result['action'],
            'in_wishlist' => $result['in_wishlist'],
            'message' => $result['message'],
        ]);
    }
}
