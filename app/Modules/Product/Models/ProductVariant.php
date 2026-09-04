<?php

namespace App\Modules\Product\Models;

use App\Modules\Media\Traits\HasMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    use HasFactory, HasMedia;

    protected $table = 'product_variants';

    protected $fillable = [
        'product_id',
        'sku',
        'barcode',
        'price',
        'compare_at_price',
        'cost_price',
        'stock_quantity',
        'manage_stock',
        'is_active',
        'image_url',
        'weight',
        'dimensions',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'manage_stock' => 'boolean',
        'is_active' => 'boolean',
        'weight' => 'decimal:2',
        'dimensions' => 'array',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function variantValues(): HasMany
    {
        return $this->hasMany(ProductVariantValue::class, 'variant_id');
    }

    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductAttributeValue::class,
            'product_variant_values',
            'variant_id',
            'attribute_value_id'
        )->withPivot('attribute_id');
    }

    public function isInStock(): bool
    {
        if (! $this->manage_stock) {
            return true;
        }

        return $this->stock_quantity > 0;
    }
}
