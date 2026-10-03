<?php

declare(strict_types=1);

namespace App\Modules\Vendor\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVendorStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $storeId = $this->user()?->vendorStore?->id;

        return [
            'name'        => 'required|string|max:255',
            'slug'        => ['nullable', 'string', 'max:255', Rule::unique('vendor_stores', 'slug')->ignore($storeId)],
            'description' => 'nullable|string',
            'phone'       => 'nullable|string|max:30',
            'email'       => 'nullable|email|max:255',
            'address'     => 'nullable|string|max:255',
            'logo'        => 'nullable|image|max:5120',
            'banner'      => 'nullable|image|max:10240',
        ];
    }
}
