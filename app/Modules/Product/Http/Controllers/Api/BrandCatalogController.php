<?php

namespace App\Modules\Product\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Product\Http\Resources\BrandResource;
use App\Modules\Product\Http\Resources\ProductListResource;
use App\Modules\Product\Models\ProductBrand;
use App\Modules\Product\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BrandCatalogController extends Controller
{
    public function __construct(protected ProductService $productService)
    {
    }

    /**
     * Get active brands list
     */
    public function index(): JsonResponse
    {
        $brands = ProductBrand::with('media')
            ->where('is_active', true)
            ->withCount('products')
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => BrandResource::collection($brands),
        ]);
    }

    /**
     * Get single brand details with products
     */
    public function show(string $slug, Request $request): JsonResponse
    {
        $brand = ProductBrand::with('media')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (! $brand) {
            return response()->json([
                'success' => false,
                'message' => 'Brand not found.',
            ], 404);
        }

        $filters = array_merge($request->all(), ['brand' => $brand->id]);
        $products = $this->productService->getFilteredCatalog($filters, (int) $request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'brand' => new BrandResource($brand),
            'products' => ProductListResource::collection($products),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }
}
