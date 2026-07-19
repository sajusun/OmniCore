<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGarageRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Dynamic access handling: Current user target resource entity update permission check rules mapping
        $garage = $this->route('garage');
        return $garage && $garage->user_id === auth()->id();
    }

    public function rules(): array
    {
        $garage = $this->route('garage');
        $garageId = $garage ? $garage->id : null;

        return [
            'name'        => ['sometimes', 'required', 'string', 'max:255'],
            'slug'        => ['nullable', 'string', 'max:255', "unique:garages,slug,{$garageId}"], // Safe duplicate exception check
            'description' => ['nullable', 'string'],
            'logo'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'banner'      => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'location'    => ['nullable', 'string', 'max:500'],
            'visibility'  => ['sometimes', 'boolean'],
        ];
    }
}