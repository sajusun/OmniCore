<?php

namespace App\Modules\Product\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Product\Http\Resources\ProductListResource;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function __construct(protected ProductService $productService)
    {
    }

    /**
     * Get products catalog with deep multi-facet filters
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'search',
            'category',
            'brand',
            'min_price',
            'max_price',
            'in_stock',
            'featured',
            'rating',
            'sort',
        ]);

        $perPage = (int) $request->get('per_page', 20);
        $products = $this->productService->getFilteredCatalog($filters, $perPage);

        return response()->json([
            'success' => true,
            'message' => 'Products fetched successfully.',
            'data' => ProductListResource::collection($products),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    /**
     * Get featured showcase products
     */
    public function featured(Request $request): JsonResponse
    {
        $limit = (int) $request->get('limit', 8);

        $products = Product::with(['category', 'brand', 'media'])
            ->published()
            ->featured()
            ->latest()
            ->take($limit)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Featured products fetched successfully.',
            'data' => ProductListResource::collection($products),
        ]);
    }

    /**
     * Fast autocomplete preview for search bar dropdowns
     */
    public function autocomplete(Request $request): JsonResponse
    {
        $q = trim((string) $request->get('q', ''));

        if (empty($q) || strlen($q) < 2) {
            return response()->json([
                'success' => true,
                'data' => [],
            ]);
        }

        $searchTerm = "%{$q}%";
        $products = Product::with(['category', 'media'])
            ->published()
            ->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', $searchTerm)
                    ->orWhere('sku', 'like', $searchTerm);
            })
            ->take(6)
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'price' => (float) $p->price,
                'compare_at_price' => $p->compare_at_price ? (float) $p->compare_at_price : null,
                'thumbnail' => $p->thumbnail_url,
                'category' => $p->category?->name,
                'average_rating' => (float) $p->average_rating,
            ]);

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * "Frequently Bought Together" Bundle Recommendations
     */
    public function bundleRecommendations(string $slug, Request $request): JsonResponse
    {
        $mainProduct = Product::with(['category', 'media'])->where('slug', $slug)->firstOrFail();

        // Get 2 complementary products from same/related categories
        $bundleItems = Product::with(['category', 'media'])
            ->published()
            ->where('id', '!=', $mainProduct->id)
            ->where(function ($q) use ($mainProduct) {
                if ($mainProduct->category_id) {
                    $q->where('category_id', $mainProduct->category_id);
                }
            })
            ->take(2)
            ->get();

        $allBundle = collect([$mainProduct])->merge($bundleItems);
        $totalOriginalPrice = $allBundle->sum('price');
        $bundleDiscountPercent = 10; // 10% bundle discount
        $bundlePrice = round($totalOriginalPrice * (1 - ($bundleDiscountPercent / 100)), 2);

        return response()->json([
            'success' => true,
            'data' => [
                'main_product' => new ProductListResource($mainProduct),
                'bundle_items' => ProductListResource::collection($bundleItems),
                'total_regular_price' => (float) $totalOriginalPrice,
                'bundle_price' => (float) $bundlePrice,
                'bundle_discount_percentage' => $bundleDiscountPercent,
                'savings' => round($totalOriginalPrice - $bundlePrice, 2),
            ],
        ]);
    }
}

