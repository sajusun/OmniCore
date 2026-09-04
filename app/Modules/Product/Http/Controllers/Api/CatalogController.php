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
}
