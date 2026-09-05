<?php

namespace App\Modules\Interaction\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Bookmark extends Model
{
    protected $table = 'bookmarks';

    protected $fillable = [
        'user_id',
        'bookmarkable_type',
        'bookmarkable_id',
        'collection',
    ];

    /**
     * Get the target model that was bookmarked / saved.
     */
    public function bookmarkable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who bookmarked the item.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
