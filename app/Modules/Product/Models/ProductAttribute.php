<?php

namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductAttribute extends Model
{
    use HasFactory;

    protected $table = 'product_attributes';

    protected $fillable = [
        'name',
        'slug',
        'type', // select, color, button
    ];

    public function values(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class, 'attribute_id')->orderBy('order', 'asc');
    }
}
