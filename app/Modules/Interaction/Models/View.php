<?php

namespace App\Modules\Interaction\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class View extends Model
{
    protected $table = 'views';

    protected $fillable = [
        'user_id',
        'viewable_type',
        'viewable_id',
        'ip_address',
        'user_agent',
    ];

    /**
     * Get the target model that was viewed.
     */
    public function viewable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who viewed (if authenticated).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
