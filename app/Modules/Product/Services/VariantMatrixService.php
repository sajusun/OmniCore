<?php

namespace App\Modules\Product\Services;

use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\ProductAttributeValue;
use App\Modules\Product\Models\ProductVariant;
use App\Modules\Product\Models\ProductVariantValue;
use Illuminate\Support\Str;

class VariantMatrixService
{
    /**
     * Generate Cartesian Product of attribute values and create ProductVariant records
     *
     * @param  array  $attributeValueIds  e.g. [[1, 2], [5, 6, 7]] (array of attribute value arrays)
     * @param  array  $options  Optional base price, manage_stock, default stock_quantity
     * @return array Created variants
     */
    public function generateMatrix(Product $product, array $attributeValueIds, array $options = []): array
    {
        if (empty($attributeValueIds)) {
            return [];
        }

        // Generate combinations via Cartesian product
        $combinations = $this->cartesian($attributeValueIds);
        $createdVariants = [];

        foreach ($combinations as $combo) {
            // $combo is an array of attribute_value_ids e.g. [1, 5]
            $valueModels = ProductAttributeValue::with('attribute')->whereIn('id', (array) $combo)->get();

            // Build deterministic SKU e.g. PROD-RED-XL
            $skuSuffix = $valueModels->map(fn ($v) => Str::upper(Str::slug($v->value)))->implode('-');
            $baseSku = $product->sku ?: Str::upper(Str::slug($product->name));
            $variantSku = $baseSku.'-'.$skuSuffix;

            // Ensure unique SKU if already exists
            $skuCount = ProductVariant::where('sku', $variantSku)->count();
            if ($skuCount > 0) {
                $variantSku .= '-'.($skuCount + 1);
            }

            // Create or find variant
            $variant = ProductVariant::create([
                'product_id' => $product->id,
                'sku' => $variantSku,
                'price' => $options['price'] ?? $product->price,
                'compare_at_price' => $options['compare_at_price'] ?? $product->compare_at_price,
                'cost_price' => $options['cost_price'] ?? $product->cost_price,
                'stock_quantity' => $options['stock_quantity'] ?? $product->stock_quantity,
                'manage_stock' => $options['manage_stock'] ?? true,
                'is_active' => true,
            ]);

            // Attach pivot values
            foreach ($valueModels as $val) {
                ProductVariantValue::create([
                    'variant_id' => $variant->id,
                    'attribute_id' => $val->attribute_id,
                    'attribute_value_id' => $val->id,
                ]);
            }

            $createdVariants[] = $variant->load('attributeValues.attribute');
        }

        // Set product type to variable if not already
        if ($product->type !== 'variable') {
            $product->update(['type' => 'variable']);
        }

        return $createdVariants;
    }

    /**
     * Compute Cartesian Product of array of arrays
     */
    protected function cartesian(array $input): array
    {
        $result = [[]];

        foreach ($input as $key => $values) {
            $append = [];
            foreach ($result as $product) {
                foreach ($values as $item) {
                    $productCopy = $product;
                    $productCopy[] = $item;
                    $append[] = $productCopy;
                }
            }
            $result = $append;
        }

        return $result;
    }
}
