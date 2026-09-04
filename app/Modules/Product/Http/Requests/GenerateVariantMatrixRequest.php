<?php

namespace App\Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateVariantMatrixRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'attribute_value_ids' => 'required|array|min:1',
            'attribute_value_ids.*' => 'required|array|min:1',
            'attribute_value_ids.*.*' => 'required|integer|exists:product_attribute_values,id',
            'options' => 'nullable|array',
            'options.price' => 'nullable|numeric|min:0',
            'options.compare_at_price' => 'nullable|numeric|min:0',
            'options.cost_price' => 'nullable|numeric|min:0',
            'options.stock_quantity' => 'nullable|integer|min:0',
            'options.manage_stock' => 'nullable|boolean',
        ];
    }
}
