<?php

namespace App\Modules\Interaction\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Like extends Model
{
    protected $table = 'likes';

    protected $fillable = [
        'user_id',
        'likeable_type',
        'likeable_id',
        'type',
    ];

    /**
     * Get the target model that was liked.
     */
    public function likeable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who performed the like.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
