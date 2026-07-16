<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodLog extends Model
{
    protected $fillable = [
        'user_id',
        'food_scan_id',
        'product_name',
        'image',
        'meal_type',
        'serving_size',
        'calories',
        'protein_g',
        'carbs_g',
        'fat_g',
        'food_score',
        'logged_date',
        'logged_time',
    ];

    protected $casts = [
        'logged_date' => 'date',
        'logged_time' => 'datetime:H:i:s',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scan()
    {
        return $this->belongsTo(FoodScan::class, 'food_scan_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
