<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGarageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'slug'        => ['nullable', 'string', 'max:255', 'unique:garages,slug'],
            'description' => ['nullable', 'string'],
            'logo'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'], // Max 2MB file size configuration
            'banner'      => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'], // Max 4MB size framework structure
            'location'    => ['nullable', 'string', 'max:500'],
            'visibility'  => ['nullable', 'boolean'],
            'user_id'     => ['nullable', 'exists:users,id'], // Backend automatic override dynamic safe validation fallback mapping
        ];
    }
}