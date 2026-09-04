<?php

namespace App\Modules\Product\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Product\Http\Resources\ProductDetailResource;
use App\Modules\Product\Http\Resources\ProductListResource;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductDetailController extends Controller
{
    public function __construct(protected ProductService $productService)
    {
    }

    /**
     * Get single product details by slug
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        $product = $this->productService->getProductBySlug($slug);

        if (! $product || $product->status !== 'published') {
            return response()->json([
                'success' => false,
                'message' => 'Product not found or unavailable.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new ProductDetailResource($product),
        ]);
    }

    /**
     * Get related products based on category / brand
     */
    public function related(string $slug, Request $request): JsonResponse
    {
        $product = Product::where('slug', $slug)->first();

        if (! $product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.',
            ], 404);
        }

        $limit = (int) $request->get('limit', 4);

        $related = Product::with(['category', 'brand', 'media'])
            ->published()
            ->where('id', '!=', $product->id)
            ->where(function ($q) use ($product) {
                if ($product->category_id) {
                    $q->where('category_id', $product->category_id);
                }
                if ($product->brand_id) {
                    $q->orWhere('brand_id', $product->brand_id);
                }
            })
            ->take($limit)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Related products fetched.',
            'data' => ProductListResource::collection($related),
        ]);
    }
}
