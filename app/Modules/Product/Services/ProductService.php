<?php

namespace App\Modules\Product\Services;

use App\Modules\Media\Services\MediaService;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\ProductCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ProductService
{
    public function __construct(protected MediaService $mediaService) {}

    /**
     * Deep Multi-Facet Catalog Filtering Engine
     */
    public function getFilteredCatalog(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = Product::with(['category', 'brand', 'media', 'activeVariants.attributeValues'])
            ->published();

        // 1. Full-text search (Name, SKU, Short Description)
        if (! empty($filters['search'])) {
            $searchTerm = '%'.$filters['search'].'%';
            $query->where(function (Builder $q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                    ->orWhere('sku', 'like', $searchTerm)
                    ->orWhere('short_description', 'like', $searchTerm);
            });
        }

        // 2. Category Filter (by slug or ID, including child categories)
        if (! empty($filters['category'])) {
            $category = is_numeric($filters['category'])
                ? ProductCategory::find($filters['category'])
                : ProductCategory::where('slug', $filters['category'])->first();

            if ($category) {
                $categoryIds = $this->getAllCategoryIds($category);
                $query->whereIn('category_id', $categoryIds);
            }
        }

        // 3. Brand Filter
        if (! empty($filters['brand'])) {
            $brand = $filters['brand'];
            if (is_numeric($brand)) {
                $query->where('brand_id', $brand);
            } else {
                $query->whereHas('brand', fn ($b) => $b->where('slug', $brand));
            }
        }

        // 4. Price Range (min_price and max_price)
        if (isset($filters['min_price']) && is_numeric($filters['min_price'])) {
            $query->where('price', '>=', (float) $filters['min_price']);
        }
        if (isset($filters['max_price']) && is_numeric($filters['max_price'])) {
            $query->where('price', '<=', (float) $filters['max_price']);
        }

        // 5. Stock Status
        if (isset($filters['in_stock']) && filter_var($filters['in_stock'], FILTER_VALIDATE_BOOLEAN)) {
            $query->where('is_in_stock', true);
        }

        // 6. Featured Only
        if (isset($filters['featured']) && filter_var($filters['featured'], FILTER_VALIDATE_BOOLEAN)) {
            $query->where('is_featured', true);
        }

        // 7. Minimum Rating (e.g., 4+ stars)
        if (! empty($filters['rating']) && is_numeric($filters['rating'])) {
            $query->where('average_rating', '>=', (float) $filters['rating']);
        }

        // 8. Sorting
        $sort = $filters['sort'] ?? 'newest';
        switch ($sort) {
            case 'price_low_high':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high_low':
                $query->orderBy('price', 'desc');
                break;
            case 'popular':
                $query->orderBy('views_count', 'desc');
                break;
            case 'best_selling':
                $query->orderBy('sales_count', 'desc');
                break;
            case 'rating':
                $query->orderBy('average_rating', 'desc');
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        return $query->paginate($perPage);
    }

    /**
     * Get single product by slug with view increment & relations
     */
    public function getProductBySlug(string $slug): ?Product
    {
        $product = Product::with([
            'category.parent',
            'brand',
            'media',
            'activeVariants.attributeValues.attribute',
            'approvedReviews.user:id,name,avatar',
            'approvedReviews.media',
            'tags',
        ])
            ->where('slug', $slug)
            ->first();

        if ($product) {
            $product->increment('views_count');
        }

        return $product;
    }

    /**
     * Create product with media
     */
    public function createProduct(array $data, ?UploadedFile $thumbnail = null, array $gallery = []): Product
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Ensure unique slug
        $slugCount = Product::where('slug', $data['slug'])->count();
        if ($slugCount > 0) {
            $data['slug'] .= '-'.($slugCount + 1);
        }

        if (empty($data['sku']) && ! empty($data['name'])) {
            $data['sku'] = Str::upper(Str::slug(Str::substr($data['name'], 0, 8))).'-'.rand(1000, 9999);
        }

        // Auto manage stock flag
        if (isset($data['stock_quantity'])) {
            $data['is_in_stock'] = (int) $data['stock_quantity'] > 0;
        }

        $product = Product::create($data);

        // Upload Thumbnail
        if ($thumbnail) {
            $this->mediaService->upload($thumbnail, $product, 'thumbnail', 'products');
        }

        // Upload Gallery Images
        if (! empty($gallery)) {
            foreach ($gallery as $image) {
                if ($image instanceof UploadedFile) {
                    $this->mediaService->upload($image, $product, 'gallery', 'products');
                }
            }
        }

        return $product->load(['category', 'brand', 'media', 'variants']);
    }

    /**
     * Update product
     */
    public function updateProduct(Product $product, array $data, ?UploadedFile $thumbnail = null, array $gallery = []): Product
    {
        if (isset($data['name']) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        if (isset($data['stock_quantity'])) {
            $data['is_in_stock'] = (int) $data['stock_quantity'] > 0;
        }

        $product->update($data);

        if ($thumbnail) {
            $product->media()->where('collection_name', 'thumbnail')->delete();
            $this->mediaService->upload($thumbnail, $product, 'thumbnail', 'products');
        }

        if (! empty($gallery)) {
            foreach ($gallery as $image) {
                if ($image instanceof UploadedFile) {
                    $this->mediaService->upload($image, $product, 'gallery', 'products');
                }
            }
        }

        return $product->fresh(['category', 'brand', 'media', 'variants']);
    }

    /**
     * Bulk update status
     */
    public function bulkUpdateStatus(array $productIds, string $status): int
    {
        return Product::whereIn('id', $productIds)->update(['status' => $status]);
    }

    /**
     * Bulk price adjustment (% or fixed)
     */
    public function bulkUpdatePrices(array $productIds, string $type, float $value): int
    {
        $products = Product::whereIn('id', $productIds)->get();
        $updated = 0;

        foreach ($products as $product) {
            $oldPrice = (float) $product->price;
            $newPrice = $type === 'percentage'
                ? $oldPrice + ($oldPrice * ($value / 100))
                : $oldPrice + $value;

            $product->update([
                'compare_at_price' => $oldPrice,
                'price' => max(0, round($newPrice, 2)),
            ]);
            $updated++;
        }

        return $updated;
    }

    /**
     * Helper to collect all child category IDs recursively
     */
    protected function getAllCategoryIds(ProductCategory $category): array
    {
        $ids = [$category->id];
        foreach ($category->children as $child) {
            $ids = array_merge($ids, $this->getAllCategoryIds($child));
        }

        return $ids;
    }
}
