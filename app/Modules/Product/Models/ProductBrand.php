<?php

namespace App\Modules\Product\Models;

use App\Modules\Media\Traits\HasMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductBrand extends Model
{
    use HasFactory, HasMedia, SoftDeletes;

    protected $table = 'product_brands';

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'website',
        'description',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'brand_id');
    }
}
