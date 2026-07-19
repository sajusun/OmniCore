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
        'bio',
        'country',
        'state',
        'city',
        'zip_code',
        'address',
        'website',
    ];

    protected $casts = [
        'latitude'  => 'float',
        'longitude' => 'float',
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
