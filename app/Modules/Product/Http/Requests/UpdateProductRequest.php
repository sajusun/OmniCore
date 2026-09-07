<?php

namespace App\Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id ?? $this->route('product') ?? $this->input('id');

        return [
            'category_id' => 'nullable|exists:product_categories,id',
            'brand_id' => 'nullable|exists:product_brands,id',
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug,'.$productId,
            'sku' => 'nullable|string|max:100|unique:products,sku,'.$productId,
            'type' => 'nullable|in:simple,variable,digital',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric',
            'cost_price' => 'nullable|numeric|min:0',
            'manage_stock' => 'nullable|boolean',
            'stock_quantity' => 'nullable|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'allow_backorders' => 'nullable|boolean',
            'status' => 'nullable|in:draft,published,archived',
            'is_featured' => 'nullable|boolean',
            'is_refundable' => 'nullable|boolean',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|array',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:10240',
            'gallery' => 'nullable|array',
            'gallery.*' => 'image|max:10240',
        ];
    }
}
