<?php

namespace App\Modules\Reward\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TierStoreUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tierId = $this->route('tier')?->id ?? $this->input('id');

        return [
            'name' => ['required', 'string', 'max:50'],
            'slug' => ['nullable', 'string', 'max:50', 'unique:reward_tiers,slug,' . $tierId],
            'description' => ['nullable', 'string'],
            'min_points' => ['required', 'integer', 'min:0'],
            'point_multiplier' => ['required', 'numeric', 'min:1.0', 'max:10.0'],
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'perks' => ['nullable', 'array'],
            'color' => ['nullable', 'string', 'max:20'],
            'sort_order' => ['nullable', 'integer'],
        ];
    }
}
