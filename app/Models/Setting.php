<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            cache()->forget('settings');
        });

        static::deleted(function () {
            cache()->forget('settings');
        });
    }

    protected $fillable = [
        'name',
        'title',
        'description',
        'phone',
        'email',
        'copyright',
        'keywords',
        'author',
        'address',
        'favicon',
        'thumbnail',
        'map_embed_code',
        'business_time',
        'logo',
        'logo_width',
        'logo_height',
    ];

    protected $casts = [
        'map_embed_code' => 'string',
    ];

    public function getLogoAttribute($value): string | null
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

    public function getFaviconAttribute($value): string | null
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
    
}
