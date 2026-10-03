<?php

declare(strict_types=1);

namespace App\Modules\Vendor\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterVendorStoreRequest extends FormRequest
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
        return [
            'name'        => 'required|string|max:255',
            'slug'        => 'nullable|string|max:255|unique:vendor_stores,slug',
            'description' => 'nullable|string',
            'phone'       => 'nullable|string|max:30',
            'email'       => 'nullable|email|max:255',
            'address'     => 'nullable|string|max:255',
            'logo'        => 'nullable|image|max:5120',
            'banner'      => 'nullable|image|max:10240',
        ];
    }
}
