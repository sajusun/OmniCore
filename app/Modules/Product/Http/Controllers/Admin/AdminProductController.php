<?php

namespace App\Modules\Product\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Product\Http\Requests\StoreProductRequest;
use App\Modules\Product\Http\Requests\UpdateProductRequest;
use App\Modules\Product\Http\Resources\ProductDetailResource;
use App\Modules\Product\Http\Resources\ProductListResource;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminProductController extends Controller
{
    public function __construct(protected ProductService $productService)
    {
    }

    /**
     * List all products for admin table
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::with(['category', 'brand', 'media']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $s = '%' . $request->search . '%';
            $query->where(fn ($q) => $q->where('name', 'like', $s)->orWhere('sku', 'like', $s));
        }

        $products = $query->latest()->paginate((int) $request->get('per_page', 20));

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
     * Store new product
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $data = $request->validated();
        $thumbnail = $request->file('thumbnail');
        $gallery = $request->file('gallery', []);

        $product = $this->productService->createProduct($data, $thumbnail, $gallery);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully.',
            'data' => new ProductDetailResource($product),
        ], 201);
    }

    /**
     * Show single product details for editing
     */
    public function show(int $id): JsonResponse
    {
        $product = Product::with([
            'category',
            'brand',
            'media',
            'variants.attributeValues.attribute',
            'tags',
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new ProductDetailResource($product),
        ]);
    }

    /**
     * Update product
     */
    public function update(int $id, UpdateProductRequest $request): JsonResponse
    {
        $product = Product::findOrFail($id);
        $data = $request->validated();
        $thumbnail = $request->file('thumbnail');
        $gallery = $request->file('gallery', []);

        $updatedProduct = $this->productService->updateProduct($product, $data, $thumbnail, $gallery);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully.',
            'data' => new ProductDetailResource($updatedProduct),
        ]);
    }

    /**
     * Delete product (soft delete)
     */
    public function destroy(int $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.',
        ]);
    }

    /**
     * Bulk update product status (publish, draft, archive)
     */
    public function bulkStatus(Request $request): JsonResponse
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:products,id',
            'status' => 'required|in:draft,published,archived',
        ]);

        $count = $this->productService->bulkUpdateStatus($request->ids, $request->status);

        return response()->json([
            'success' => true,
            'message' => "Updated status for {$count} products.",
        ]);
    }

    /**
     * Bulk update prices
     */
    public function bulkPrices(Request $request): JsonResponse
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:products,id',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric',
        ]);

        $count = $this->productService->bulkUpdatePrices($request->ids, $request->type, (float) $request->value);

        return response()->json([
            'success' => true,
            'message' => "Updated prices for {$count} products.",
        ]);
    }
}
