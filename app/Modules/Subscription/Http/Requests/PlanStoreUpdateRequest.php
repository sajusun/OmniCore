<?php

namespace App\Modules\Subscription\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlanStoreUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $planId = $this->route('plan')?->id ?? $this->input('id');

        return [
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:100', 'unique:plans,slug,' . $planId],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'signup_fee' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'billing_interval' => ['required', 'string', 'in:day,week,month,year,lifetime'],
            'interval_count' => ['nullable', 'integer', 'min:1'],
            'trial_days' => ['nullable', 'integer', 'min:0'],
            'is_popular' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
            'features' => ['nullable', 'array'],
            'features.*.name' => ['required_with:features', 'string'],
            'features.*.code' => ['required_with:features', 'string'],
            'features.*.value' => ['required_with:features', 'string'],
            'features.*.is_limited' => ['nullable', 'boolean'],
        ];
    }
}
