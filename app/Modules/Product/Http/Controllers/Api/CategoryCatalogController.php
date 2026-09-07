<?php

namespace App\Modules\Product\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Product\Http\Resources\CategoryResource;
use App\Modules\Product\Http\Resources\ProductListResource;
use App\Modules\Product\Models\ProductCategory;
use App\Modules\Product\Services\CategoryService;
use App\Modules\Product\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryCatalogController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService,
        protected ProductService $productService
    ) {
        parent::__construct();
    }

    /**
     * Get nested category tree
     */
    public function tree(): JsonResponse
    {
        $categories = $this->categoryService->getTree(true);

        return $this->success(
            CategoryResource::collection($categories),
            'Category tree fetched successfully.'
        );
    }

    /**
     * Get products in category with subcategories & breadcrumbs
     */
    public function show(string $slug, Request $request): JsonResponse
    {
        $category = ProductCategory::with(['children', 'media'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (! $category) {
            return $this->notFound('Category not found.');
        }

        $filters = array_merge($request->all(), ['category' => $category->id]);
        $products = $this->productService->getFilteredCatalog($filters, (int) $request->get('per_page', 20));

        return $this->success([
            'category' => new CategoryResource($category),
            'breadcrumbs' => $category->getBreadcrumbs(),
            'products' => ProductListResource::collection($products),
        ], 'Category products fetched successfully.', 200, [
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
            'per_page' => $products->perPage(),
            'total' => $products->total(),
        ]);
    }
}
