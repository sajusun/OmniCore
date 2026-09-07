<?php

namespace App\Modules\Interaction\Traits;

use App\Models\User;
use App\Modules\Interaction\Models\ShareLink;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

trait HasShareLinks
{
    /**
     * Get all share links generated for this model.
     */
    public function shareLinks(): MorphMany
    {
        return $this->morphMany(ShareLink::class, 'shareable');
    }

    /**
     * Get total share links count.
     */
    public function sharesCount(): int
    {
        return $this->shareLinks()->count();
    }

    /**
     * Get total clicks across all share links for this model.
     */
    public function totalShareClicksCount(): int
    {
        return (int) $this->shareLinks()->sum('click_count');
    }

    /**
     * Get or create a public SEO-friendly share link.
     */
    public function getPublicShareLink(?User $user = null): ShareLink
    {
        $slug = $this->resolveShareableSeoSlug();

        return $this->shareLinks()->firstOrCreate(
            [
                'type' => 'public',
                'slug' => $slug,
            ],
            [
                'user_id' => $user?->id,
                'is_active' => true,
            ]
        );
    }

    /**
     * Get the public shareable URL.
     */
    public function getPublicShareUrl(?User $user = null): string
    {
        return $this->getPublicShareLink($user)->getUrl();
    }

    /**
     * Create a private expiring or single-use share link.
     */
    public function createPrivateShareLink(
        ?DateTimeInterface $expiresAt = null,
        ?int $maxClicks = 1,
        ?User $user = null,
        string $type = 'private'
    ): ShareLink {
        $token = 'sec_'.Str::random(32).'_'.dechex(time());

        return $this->shareLinks()->create([
            'user_id' => $user?->id,
            'type' => $maxClicks === 1 ? 'single_use' : ($expiresAt ? 'expiring' : $type),
            'token' => $token,
            'expires_at' => $expiresAt,
            'max_clicks' => $maxClicks,
            'click_count' => 0,
            'is_active' => true,
        ]);
    }

    /**
     * Resolve SEO slug dynamically from model attributes.
     */
    public function resolveShareableSeoSlug(): string
    {
        if (method_exists($this, 'getShareableSlug')) {
            return $this->getShareableSlug();
        }

        if (! empty($this->slug)) {
            return (string) $this->slug;
        }

        if (! empty($this->title)) {
            return Str::slug($this->title).'-'.$this->getKey();
        }

        if (! empty($this->name)) {
            return Str::slug($this->name).'-'.$this->getKey();
        }

        return (string) $this->getKey();
    }
}
