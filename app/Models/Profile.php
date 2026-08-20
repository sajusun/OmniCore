<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'phone',
        'date_of_birth',
        'gender',
        'profile_photo',
        'cover_photo',
        'bio',
        'country',
        'state',
        'city',
        'zip_code',
        'address',
        'website',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function getCoverPhotoAttribute($value): ?string
    {
        if (empty($value)) {
            return null;
        }
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        if (request()->is('api/*')) {
            return url($value);
        }
        return $value;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
