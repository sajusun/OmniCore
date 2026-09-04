<?php

namespace App\Modules\Product\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Product\Models\ProductAttribute;
use App\Modules\Product\Models\ProductAttributeValue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminAttributeController extends Controller
{
    public function index(): JsonResponse
    {
        $attributes = ProductAttribute::with('values')->get();

        return response()->json([
            'success' => true,
            'data' => $attributes,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:product_attributes,slug',
            'type' => 'nullable|in:select,color,button',
            'values' => 'nullable|array',
            'values.*.value' => 'required_with:values|string|max:255',
            'values.*.code' => 'nullable|string|max:50',
            'values.*.order' => 'nullable|integer',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $attribute = ProductAttribute::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'type' => $validated['type'] ?? 'select',
        ]);

        if (! empty($validated['values'])) {
            foreach ($validated['values'] as $val) {
                $attribute->values()->create($val);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Attribute created successfully.',
            'data' => $attribute->load('values'),
        ], 201);
    }

    public function addValue(int $attributeId, Request $request): JsonResponse
    {
        $attribute = ProductAttribute::findOrFail($attributeId);

        $validated = $request->validate([
            'value' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'order' => 'nullable|integer',
        ]);

        $value = $attribute->values()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Attribute value added.',
            'data' => $value,
        ], 201);
    }

    public function deleteValue(int $attributeId, int $valueId): JsonResponse
    {
        $value = ProductAttributeValue::where('attribute_id', $attributeId)->findOrFail($valueId);
        $value->delete();

        return response()->json([
            'success' => true,
            'message' => 'Attribute value deleted.',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $attribute = ProductAttribute::findOrFail($id);
        $attribute->delete();

        return response()->json([
            'success' => true,
            'message' => 'Attribute deleted successfully.',
        ]);
    }
}
