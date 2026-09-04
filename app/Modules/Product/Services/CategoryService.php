<?php

namespace App\Modules\Product\Services;

use App\Modules\Media\Services\MediaService;
use App\Modules\Product\Models\ProductCategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class CategoryService
{
    public function __construct(protected MediaService $mediaService)
    {
    }

    /**
     * Get nested category tree
     */
    public function getTree(bool $onlyActive = true): Collection
    {
        $query = ProductCategory::with(['children.children', 'media'])
            ->whereNull('parent_id')
            ->orderBy('order', 'asc');

        if ($onlyActive) {
            $query->where('is_active', true);
        }

        return $query->get();
    }

    /**
     * Create category with slug & media handling
     */
    public function create(array $data, ?UploadedFile $image = null, ?UploadedFile $icon = null): ProductCategory
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $category = ProductCategory::create($data);

        if ($image) {
            $this->mediaService->upload($image, $category, 'image', 'categories');
        }

        if ($icon) {
            $this->mediaService->upload($icon, $category, 'icon', 'categories');
        }

        return $category;
    }

    /**
     * Update category
     */
    public function update(ProductCategory $category, array $data, ?UploadedFile $image = null, ?UploadedFile $icon = null): ProductCategory
    {
        if (isset($data['name']) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $category->update($data);

        if ($image) {
            $category->media()->where('collection_name', 'image')->delete();
            $this->mediaService->upload($image, $category, 'image', 'categories');
        }

        if ($icon) {
            $category->media()->where('collection_name', 'icon')->delete();
            $this->mediaService->upload($icon, $category, 'icon', 'categories');
        }

        return $category;
    }
}
