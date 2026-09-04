<?php

namespace App\Modules\Product\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Product\Http\Requests\StoreCategoryRequest;
use App\Modules\Product\Http\Resources\CategoryResource;
use App\Modules\Product\Models\ProductCategory;
use App\Modules\Product\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminCategoryController extends Controller
{
    public function __construct(protected CategoryService $categoryService)
    {
    }

    public function index(): JsonResponse
    {
        $categories = $this->categoryService->getTree(false);

        return response()->json([
            'success' => true,
            'data' => CategoryResource::collection($categories),
        ]);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->create(
            $request->validated(),
            $request->file('image'),
            $request->file('icon')
        );

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully.',
            'data' => new CategoryResource($category),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $category = ProductCategory::with('children')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new CategoryResource($category),
        ]);
    }

    public function update(int $id, StoreCategoryRequest $request): JsonResponse
    {
        $category = ProductCategory::findOrFail($id);
        $updatedCategory = $this->categoryService->update(
            $category,
            $request->validated(),
            $request->file('image'),
            $request->file('icon')
        );

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully.',
            'data' => new CategoryResource($updatedCategory),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $category = ProductCategory::findOrFail($id);
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully.',
        ]);
    }
}
