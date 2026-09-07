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
        parent::__construct();
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

        return $this->paginated(
            $products,
            ProductListResource::class,
            'Products fetched successfully.'
        );
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

        return $this->success(
            ProductListResource::collection($products),
            'Featured products fetched successfully.'
        );
    }

    /**
     * Fast autocomplete preview for search bar dropdowns
     */
    public function autocomplete(Request $request): JsonResponse
    {
        $q = trim((string) $request->get('q', ''));

        if (empty($q) || strlen($q) < 2) {
            return $this->success([], 'Autocomplete preview fetched.');
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

        return $this->success($products, 'Autocomplete preview fetched.');
    }

    /**
     * "Frequently Bought Together" Bundle Recommendations
     */
    public function bundleRecommendations(string $slug, Request $request): JsonResponse
    {
        $mainProduct = Product::with(['category', 'media'])->where('slug', $slug)->first();

        if (! $mainProduct) {
            return $this->notFound('Product not found.');
        }

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

        return $this->success([
            'main_product' => new ProductListResource($mainProduct),
            'bundle_items' => ProductListResource::collection($bundleItems),
            'total_regular_price' => (float) $totalOriginalPrice,
            'bundle_price' => (float) $bundlePrice,
            'bundle_discount_percentage' => $bundleDiscountPercent,
            'savings' => round($totalOriginalPrice - $bundlePrice, 2),
        ], 'Bundle recommendations fetched.');
    }
}
