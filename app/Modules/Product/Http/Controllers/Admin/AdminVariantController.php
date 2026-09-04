<?php

namespace App\Modules\Product\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Media\Services\MediaService;
use App\Modules\Product\Http\Requests\GenerateVariantMatrixRequest;
use App\Modules\Product\Http\Resources\ProductVariantResource;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\ProductVariant;
use App\Modules\Product\Services\VariantMatrixService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminVariantController extends Controller
{
    public function __construct(
        protected VariantMatrixService $variantMatrixService,
        protected MediaService $mediaService
    ) {
    }

    /**
     * Auto generate Cartesian Variant Matrix for a product
     */
    public function generate(int $productId, GenerateVariantMatrixRequest $request): JsonResponse
    {
        $product = Product::findOrFail($productId);
        $attributeValueIds = $request->input('attribute_value_ids');
        $options = $request->input('options', []);

        $variants = $this->variantMatrixService->generateMatrix($product, $attributeValueIds, $options);

        return response()->json([
            'success' => true,
            'message' => 'Variant matrix generated successfully.',
            'data' => ProductVariantResource::collection(collect($variants)),
        ], 201);
    }

    /**
     * Update individual variant
     */
    public function update(int $productId, int $variantId, Request $request): JsonResponse
    {
        $variant = ProductVariant::where('product_id', $productId)->findOrFail($variantId);

        $validated = $request->validate([
            'sku' => 'sometimes|required|string|unique:product_variants,sku,' . $variant->id,
            'barcode' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric',
            'cost_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'manage_stock' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|array',
            'image' => 'nullable|image|max:10240',
        ]);

        $variant->update($validated);

        if ($request->hasFile('image')) {
            $media = $this->mediaService->upload($request->file('image'), $variant, 'variant_image', 'products');
            $variant->update(['image_url' => $media->url]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Variant updated successfully.',
            'data' => new ProductVariantResource($variant->fresh('attributeValues.attribute')),
        ]);
    }

    /**
     * Delete variant
     */
    public function destroy(int $productId, int $variantId): JsonResponse
    {
        $variant = ProductVariant::where('product_id', $productId)->findOrFail($variantId);
        $variant->delete();

        return response()->json([
            'success' => true,
            'message' => 'Variant deleted successfully.',
        ]);
    }
}
