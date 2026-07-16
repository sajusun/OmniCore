<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHomeApplianceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            // 'post_user_id'      => 'required|exists:post_users,id',
            'appliance_type' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',

            'address' => 'nullable|string|max:255',
            'apartment_unit' => 'nullable|string|max:100',

            'available_from' => 'nullable|date',

            'building_name' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:100',

            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:10',

            'condition' => 'nullable|string|max:50',

            'delivery_available' => 'boolean',

            'deposit_amount' => 'nullable|numeric|min:0',
            'rent_amount' => 'nullable|numeric|min:0',

            'rent_period' => 'nullable|in:daily,weekly,monthly,yearly',

            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',

            // Media
            'images' => 'nullable|array|min:1|',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp,svg',
            'video' => 'nullable|mimes:mp4,mov,avi,wmv',

            // 'status' => 'in:active,inactive',
        ];
    }
}
