<?php

namespace App\Modules\Product\Models;

use App\Modules\Interaction\Traits\HasInteractions;
use App\Modules\Media\Traits\HasMedia;
use App\Modules\Review\Traits\HasReviews;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, HasMedia, HasInteractions, HasReviews, SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'slug',
        'sku',
        'type', // simple, variable, digital
        'short_description',
        'description',
        'price',
        'compare_at_price',
        'cost_price',
        'manage_stock',
        'stock_quantity',
        'low_stock_threshold',
        'is_in_stock',
        'allow_backorders',
        'status', // draft, published, archived
        'is_featured',
        'is_refundable',
        'weight',
        'dimensions',
        'views_count',
        'sales_count',
        'average_rating',
        'reviews_count',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'manage_stock' => 'boolean',
        'stock_quantity' => 'integer',
        'low_stock_threshold' => 'integer',
        'is_in_stock' => 'boolean',
        'allow_backorders' => 'boolean',
        'is_featured' => 'boolean',
        'is_refundable' => 'boolean',
        'weight' => 'decimal:2',
        'dimensions' => 'array',
        'views_count' => 'integer',
        'sales_count' => 'integer',
        'average_rating' => 'decimal:2',
        'reviews_count' => 'integer',
    ];

    protected $appends = [
        'thumbnail_url',
        'discount_percentage',
        'is_variable',
        'is_simple',
    ];

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     */

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(ProductBrand::class, 'brand_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }

    public function activeVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class, 'product_id')->where('is_active', true);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class, 'product_id');
    }

    public function approvedReviews(): HasMany
    {
        return $this->hasMany(ProductReview::class, 'product_id')->where('status', 'approved');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(ProductTag::class, 'product_tag_pivot', 'product_id', 'tag_id');
    }

    /*
     |--------------------------------------------------------------------------
     | Accessors & Mutators
     |--------------------------------------------------------------------------
     */

    public function getThumbnailUrlAttribute(): ?string
    {
        $media = $this->media()->where('collection_name', 'thumbnail')->latest()->first();
        if ($media) {
            return $media->url;
        }

        return null;
    }

    public function getGalleryUrlsAttribute(): array
    {
        return $this->media()
            ->where('collection_name', 'gallery')
            ->get()
            ->map(fn ($m) => ['id' => $m->id, 'url' => $m->url, 'file_name' => $m->file_name])
            ->toArray();
    }

    public function getDiscountPercentageAttribute(): ?int
    {
        if ($this->compare_at_price && $this->compare_at_price > $this->price) {
            $diff = $this->compare_at_price - $this->price;
            return (int) round(($diff / $this->compare_at_price) * 100);
        }

        return null;
    }

    public function getIsVariableAttribute(): bool
    {
        return $this->type === 'variable';
    }

    public function getIsSimpleAttribute(): bool
    {
        return $this->type === 'simple';
    }

    public function getIsLowStockAttribute(): bool
    {
        if (! $this->manage_stock) {
            return false;
        }

        return $this->stock_quantity <= $this->low_stock_threshold && $this->stock_quantity > 0;
    }

    /*
     |--------------------------------------------------------------------------
     | Scopes
     |--------------------------------------------------------------------------
     */

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('is_in_stock', true);
    }

    /**
     * Recalculate average rating and review count
     */
    public function updateRatingStats(): void
    {
        $reviews = $this->approvedReviews();
        $this->reviews_count = $reviews->count();
        $this->average_rating = $this->reviews_count > 0 ? (float) $reviews->avg('rating') : 0.00;
        $this->saveQuietly();
    }
}
