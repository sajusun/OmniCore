<?php

namespace App\Modules\Interaction\Models;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ShareLink extends Model
{
    protected $table = 'share_links';

    protected $fillable = [
        'user_id',
        'shareable_type',
        'shareable_id',
        'type',
        'slug',
        'token',
        'expires_at',
        'max_clicks',
        'click_count',
        'is_active',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'max_clicks' => 'integer',
        'click_count' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the target model that is shared.
     */
    public function shareable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who generated this share link.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the link is expired.
     */
    public function isExpired(): bool
    {
        if ($this->expires_at === null) {
            return false;
        }

        return Carbon::now()->greaterThan($this->expires_at);
    }

    /**
     * Check if click limit has been reached.
     */
    public function isClickLimitReached(): bool
    {
        if ($this->max_clicks === null) {
            return false;
        }

        return $this->click_count >= $this->max_clicks;
    }

    /**
     * Check if link is completely valid for access.
     */
    public function isValid(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->isExpired()) {
            return false;
        }

        if ($this->isClickLimitReached()) {
            return false;
        }

        return true;
    }

    /**
     * Increment click count and auto-deactivate if limit reached.
     */
    public function recordClick(): void
    {
        $this->increment('click_count');

        if ($this->max_clicks !== null && $this->click_count >= $this->max_clicks) {
            $this->update(['is_active' => false]);
        }
    }

    /**
     * Generate the full accessible URL.
     */
    public function getUrl(): string
    {
        if ($this->type === 'public') {
            $typeAlias = class_basename($this->shareable_type);

            return url('/share/'.strtolower($typeAlias).'/'.($this->slug ?? $this->shareable_id));
        }

        return url('/s/'.$this->token);
    }
}
