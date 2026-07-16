<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodScan extends Model
{
    protected $fillable = [
        'user_id',
        'image_url',
        'product_name',
        'identified_foods',
        'ojais_score',
        'verdict_key',
        'verdict_label',
        'nutrition',
        'insight',
        'portion_estimation',
        'team_says_text',
        'one_improvement',
        'ojais_approved',
        'metabolic_score',
        'metabolic_verdict',
        'harmful_ingredients',
        'penalty_flags',
        'score_breakdown',
        'uncertainty_flag',
    ];



    public function getImageUrlAttribute($value): string | null
    {
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        // Check if the request is an API request
        if (request()->is('api/*') && !empty($value)) {
            // Return the full URL for API requests
            return url($value);
        }

        // Return only the path for web requests
        return $value;
    }







    protected $casts = [
        'identified_foods' => 'array',
        'nutrition' => 'array',
        'harmful_ingredients' => 'array',
        'penalty_flags' => 'array',
        'score_breakdown' => 'array',
        'ojais_approved' => 'boolean',
        'uncertainty_flag' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
