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
        parent::__construct();
    }

    /**
     * Get single product details by slug
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        $product = $this->productService->getProductBySlug($slug);

        if (! $product || $product->status !== 'published') {
            return $this->notFound('Product not found or unavailable.');
        }

        return $this->success(
            new ProductDetailResource($product),
            'Product details fetched successfully.'
        );
    }

    /**
     * Get related products based on category / brand
     */
    public function related(string $slug, Request $request): JsonResponse
    {
        $product = Product::where('slug', $slug)->first();

        if (! $product) {
            return $this->notFound('Product not found.');
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

        return $this->success(
            ProductListResource::collection($related),
            'Related products fetched successfully.'
        );
    }
}
