<?php

namespace App\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $merge = [];

        // Process explanations key-value arrays
        if ($this->has('explanations_keys') && $this->has('explanations_values')) {
            $keys = $this->input('explanations_keys');
            $values = $this->input('explanations_values');
            $explanations = [];
            foreach ($keys as $index => $key) {
                if (!empty($key)) {
                    // Try to decode value if it's a JSON string representing an array/object
                    $val = $values[$index] ?? null;
                    if (is_string($val) && (str_starts_with($val, '{') || str_starts_with($val, '['))) {
                        $decodedVal = json_decode($val, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $val = $decodedVal;
                        }
                    }
                    $explanations[$key] = $val;
                }
            }
            $merge['explanations'] = empty($explanations) ? null : $explanations;
        } elseif ($this->has('explanations') && is_string($this->explanations)) {
            $decoded = json_decode($this->explanations, true);
            $merge['explanations'] = is_array($decoded) ? $decoded : null;
        }

        // Process trigger_flags key-value arrays
        if ($this->has('trigger_flags_keys') && $this->has('trigger_flags_values')) {
            $keys = $this->input('trigger_flags_keys');
            $values = $this->input('trigger_flags_values');
            $flags = [];
            foreach ($keys as $index => $key) {
                if (!empty($key)) {
                    $val = $values[$index] ?? null;
                    if (is_string($val) && (str_starts_with($val, '{') || str_starts_with($val, '['))) {
                        $decodedVal = json_decode($val, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $val = $decodedVal;
                        }
                    }
                    $flags[$key] = $val;
                }
            }
            $merge['trigger_flags'] = empty($flags) ? null : $flags;
        } elseif ($this->has('trigger_flags') && is_string($this->trigger_flags)) {
            $decoded = json_decode($this->trigger_flags, true);
            $merge['trigger_flags'] = is_array($decoded) ? $decoded : null;
        }
        
        if (!empty($merge)) {
            $this->merge($merge);
        }
    }

    public function rules(): array
    {

        $productId = $this->route('product');
        if ($productId instanceof \App\Models\Product) {
            $productId = $productId->id;
        }

        return [
            'upc' => ['required', 'string', 'max:255', \Illuminate\Validation\Rule::unique('products', 'upc')->ignore($productId)],

            'name' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'image_url' => 'nullable|url',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'serving_size_text' => 'nullable|string|max:255',

            'calories' => 'nullable|numeric',
            'protein_g' => 'nullable|numeric',
            'carbs_g' => 'nullable|numeric',
            'fiber_g' => 'nullable|numeric',
            'sugar_g' => 'nullable|numeric',
            'sugar_alcohol_g' => 'nullable|numeric',
            'fat_g' => 'nullable|numeric',
            'sat_fat_g' => 'nullable|numeric',

            'ingredients_text' => 'nullable|string',

            'source' => 'nullable|string|max:255',
            'last_seen_at' => 'nullable|date',

            'verdict' => 'nullable|in:red,ojais_approved,neutral',

            'score' => 'nullable|numeric',

            'trigger_flags' => 'nullable|array',
            'trigger_flags.*' => 'string',

            'explanations' => 'nullable|array',

            'seed_oil_score' => 'nullable|numeric',
        ];
    }
}
