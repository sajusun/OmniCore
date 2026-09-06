<?php

namespace App\Modules\Reward\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BadgeStoreUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $badgeId = $this->route('badge')?->id ?? $this->input('id');

        return [
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:100', 'unique:badges,slug,' . $badgeId],
            'description' => ['nullable', 'string'],
            'badge_type' => ['required', 'string', 'in:achievement,streak,milestone,spending'],
            'points_reward' => ['nullable', 'integer', 'min:0'],
            'criteria_type' => ['nullable', 'string', 'max:50'],
            'criteria_threshold' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
