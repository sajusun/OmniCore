<?php

namespace App\Modules\Media\Traits;

use App\Modules\Media\Models\Media;

trait HasMedia
{

    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }


    public function mediaCollection(string $collection)
    {
        return $this->media()->where('collection_name', $collection);
    }

    /**
     * Get First Media
     */
    public function firstMedia(?string $collection = null)
    {
        return $this->media()->when(
            $collection,
            fn($query) => $query->where('collection_name', $collection)
        )->first();
    }

    /**
     * Get Primary Media
     */
    public function primaryMedia()
    {
        return $this->media()->where('is_primary', true)->first();
    }


    public function mediaUrl(?string $collection = null): ?string
    {

        $media = $this->firstMedia($collection);
        return $media?->url ?? asset('defaults/user-avatar.gif');
    }


    public function hasMedia(?string $collection = null): bool
    {

        return $this->media()
            ->when(
                $collection,
                fn($query) => $query->where('collection_name', $collection)
            )->exists();
    }
}
