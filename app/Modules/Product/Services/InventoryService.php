<?php

namespace App\Modules\Product\Services;

use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\ProductVariant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class InventoryService
{
    /**
     * Get low stock products
     */
    public function getLowStockProducts(int $perPage = 15): LengthAwarePaginator
    {
        return Product::with(['category', 'brand'])
            ->where('manage_stock', true)
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->orderBy('stock_quantity', 'asc')
            ->paginate($perPage);
    }

    /**
     * Adjust stock for simple product or variant
     */
    public function adjustStock(int $productId, ?int $variantId, int $quantity, string $action = 'set'): array
    {
        if ($variantId) {
            $variant = ProductVariant::findOrFail($variantId);
            $oldStock = $variant->stock_quantity;

            if ($action === 'increment') {
                $newStock = $oldStock + $quantity;
            } elseif ($action === 'decrement') {
                $newStock = max(0, $oldStock - $quantity);
            } else {
                $newStock = max(0, $quantity);
            }

            $variant->update(['stock_quantity' => $newStock]);

            return [
                'type' => 'variant',
                'id' => $variant->id,
                'sku' => $variant->sku,
                'old_stock' => $oldStock,
                'new_stock' => $newStock,
            ];
        }

        $product = Product::findOrFail($productId);
        $oldStock = $product->stock_quantity;

        if ($action === 'increment') {
            $newStock = $oldStock + $quantity;
        } elseif ($action === 'decrement') {
            $newStock = max(0, $oldStock - $quantity);
        } else {
            $newStock = max(0, $quantity);
        }

        $product->update([
            'stock_quantity' => $newStock,
            'is_in_stock' => $newStock > 0,
        ]);

        return [
            'type' => 'product',
            'id' => $product->id,
            'name' => $product->name,
            'old_stock' => $oldStock,
            'new_stock' => $newStock,
        ];
    }
}
