<?php

namespace App\Modules\Product\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Product\Http\Resources\ProductListResource;
use App\Modules\Product\Services\InventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminInventoryController extends Controller
{
    public function __construct(protected InventoryService $inventoryService) {}

    /**
     * Get low stock alerts list
     */
    public function lowStock(Request $request): JsonResponse
    {
        $products = $this->inventoryService->getLowStockProducts((int) $request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'message' => 'Low stock inventory fetched.',
            'data' => ProductListResource::collection($products),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    /**
     * Adjust stock for product or variant
     */
    public function adjust(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'variant_id' => 'nullable|integer|exists:product_variants,id',
            'quantity' => 'required|integer',
            'action' => 'nullable|in:set,increment,decrement',
        ]);

        $result = $this->inventoryService->adjustStock(
            $validated['product_id'],
            $validated['variant_id'] ?? null,
            $validated['quantity'],
            $validated['action'] ?? 'set'
        );

        return response()->json([
            'success' => true,
            'message' => 'Stock adjusted successfully.',
            'data' => $result,
        ]);
    }
}
