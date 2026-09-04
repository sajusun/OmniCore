<?php

namespace App\Modules\Product\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Media\Services\MediaService;
use App\Modules\Product\Http\Requests\StoreBrandRequest;
use App\Modules\Product\Http\Resources\BrandResource;
use App\Modules\Product\Models\ProductBrand;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class AdminBrandController extends Controller
{
    public function __construct(protected MediaService $mediaService)
    {
    }

    public function index(): JsonResponse
    {
        $brands = ProductBrand::with('media')
            ->withCount('products')
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => BrandResource::collection($brands),
        ]);
    }

    public function store(StoreBrandRequest $request): JsonResponse
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $brand = ProductBrand::create($data);

        if ($request->hasFile('logo')) {
            $media = $this->mediaService->upload($request->file('logo'), $brand, 'logo', 'brands');
            $brand->update(['logo' => $media->url]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Brand created successfully.',
            'data' => new BrandResource($brand),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $brand = ProductBrand::with('media')->withCount('products')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new BrandResource($brand),
        ]);
    }

    public function update(int $id, StoreBrandRequest $request): JsonResponse
    {
        $brand = ProductBrand::findOrFail($id);
        $data = $request->validated();

        if (isset($data['name']) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $brand->update($data);

        if ($request->hasFile('logo')) {
            $brand->media()->where('collection_name', 'logo')->delete();
            $media = $this->mediaService->upload($request->file('logo'), $brand, 'logo', 'brands');
            $brand->update(['logo' => $media->url]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Brand updated successfully.',
            'data' => new BrandResource($brand),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $brand = ProductBrand::findOrFail($id);
        $brand->delete();

        return response()->json([
            'success' => true,
            'message' => 'Brand deleted successfully.',
        ]);
    }
}
