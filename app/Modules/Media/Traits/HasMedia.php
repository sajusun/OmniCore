<?php

namespace App\Modules\Media\Traits;

use App\Modules\Media\Enums\MediaCollection;
use App\Modules\Media\Models\Media;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

trait HasMedia
{
    /**
     * Polymorphic relation to all associated media items.
     */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order', 'asc');
    }

    /**
     * Query media within a specific collection.
     */
    public function mediaCollection(string $collection)
    {
        return $this->media()->where('collection_name', $collection);
    }

    /**
     * Get first media in a collection.
     */
    public function firstMedia(?string $collection = null): ?Media
    {
        return $this->media()
            ->when($collection, fn($q) => $q->where('collection_name', $collection))
            ->first();
    }

    /**
     * Get primary media item.
     */
    public function primaryMedia(): ?Media
    {
        return $this->media()->where('is_primary', true)->first() ?? $this->media()->first();
    }

    /**
     * Get URL for media in a collection with optional fallback.
     */
    public function mediaUrl(?string $collection = null, ?string $default = null): ?string
    {
        $media = $this->firstMedia($collection);
        if ($media && $media->full_url) {
            return $media->full_url;
        }

        return $default;
    }

    /**
     * Check if model has media in a collection.
     */
    public function hasMedia(?string $collection = null): bool
    {
        return $this->media()
            ->when($collection, fn($q) => $q->where('collection_name', $collection))
            ->exists();
    }

    /**
     * Clear all media from a specific collection on this model.
     */
    public function clearMediaCollection(string $collection): void
    {
        $mediaItems = $this->mediaCollection($collection)->get();
        foreach ($mediaItems as $media) {
            $media->delete();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Model Accessors & Shorthands
    |--------------------------------------------------------------------------
    */

    /**
     * Get avatar URL with default fallback.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        // 1. Check polymorphic media first
        $mediaUrl = $this->mediaUrl(MediaCollection::AVATAR);
        if ($mediaUrl) {
            return $mediaUrl;
        }

        // 2. Check traditional 'avatar' column if exists
        $avatarColumn = $this->attributes['avatar'] ?? null;
        if (!empty($avatarColumn)) {
            return filter_var($avatarColumn, FILTER_VALIDATE_URL) ? $avatarColumn : url($avatarColumn);
        }

        // 3. Fallback default avatar
        return asset('defaults/user-avatar.gif');
    }

    /**
     * Get cover photo URL with default fallback.
     */
    public function getCoverPhotoUrlAttribute(): ?string
    {
        $mediaUrl = $this->mediaUrl(MediaCollection::COVER_PHOTO);
        if ($mediaUrl) {
            return $mediaUrl;
        }

        $coverColumn = $this->attributes['cover_photo'] ?? null;
        if (!empty($coverColumn)) {
            return filter_var($coverColumn, FILTER_VALIDATE_URL) ? $coverColumn : url($coverColumn);
        }

        return null;
    }

    /**
     * Get thumbnail URL.
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->mediaUrl(MediaCollection::THUMBNAIL);
    }

    /**
     * Get array of all gallery media URLs.
     */
    public function getGalleryUrlsAttribute(): Collection
    {
        return $this->mediaCollection(MediaCollection::GALLERY)
            ->get()
            ->map(fn(Media $media) => $media->full_url)
            ->filter();
    }
}
