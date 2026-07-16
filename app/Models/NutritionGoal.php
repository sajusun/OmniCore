<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NutritionGoal extends Model
{
    protected $fillable = [
        'user_id',
        'goal',
        'diet_style',
        'daily_calories',
        'daily_protein',
        'daily_carbs',
        'daily_fat',
        'weekly_sugar_limit',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
