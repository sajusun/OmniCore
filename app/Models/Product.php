<?php

namespace App\Models;

use App\Traits\Favoritable;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use Favoritable;

    protected $fillable = [
        'upc',
        'name',
        'brand',
        'image_url',
        'serving_size_text',
        'calories',
        'protein_g',
        'carbs_g',
        'fiber_g',
        'sugar_g',
        'sugar_alcohol_g',
        'fat_g',
        'sat_fat_g',
        'ingredients_text',
        'source',
        'last_seen_at',
        'verdict',
        'score',
        'trigger_flags',
        'explanations',
        'seed_oil_score',
    ];

    protected $casts = [
        'trigger_flags' => 'array',
        'explanations' => 'array',
        'last_seen_at' => 'datetime',

        'calories' => 'float',
        'protein_g' => 'float',
        'carbs_g' => 'float',
        'fiber_g' => 'float',
        'sugar_g' => 'float',
        'sugar_alcohol_g' => 'float',
        'fat_g' => 'float',
        'score' => 'float',
        'sat_fat_g' => 'float',
        'seed_oil_score' => 'float',
    ];
    
    public function getImageUrlAttribute($value): ?string
    {
        return $value ? asset($value) : null;
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'product_user')->withPivot(['source', 'added_at'])->withTimestamps();
    }
}
