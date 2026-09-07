<?php

namespace App\Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $brandId = $this->route('brand')?->id ?? $this->route('brand');

        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:product_brands,slug,'.$brandId,
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'logo' => 'nullable|image|max:5120',
        ];
    }
}
