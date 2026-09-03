<?php

namespace App\Modules\Media\Traits;

use App\Modules\Media\Enums\MediaCollection;
use App\Modules\Media\Services\MediaService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

trait HandlesMedia
{
    /**
     * Get or resolve MediaService instance.
     */
    protected function mediaService(): MediaService
    {
        return app(MediaService::class);
    }

    /**
     * Upload single or multiple media files for a model.
     */
    protected function uploadMedia(
        Model $model,
        mixed $files,
        string $collection = MediaCollection::DEFAULT,
        ?string $disk = null,
        array $meta = [],
        bool $isPrimary = false
    ): Collection {
        return $this->mediaService()->upload($model, $files, $collection, $disk, $meta, $isPrimary);
    }

    /**
     * Replace existing media in a collection with new file(s).
     */
    protected function updateMedia(
        Model $model,
        mixed $files,
        string $collection = MediaCollection::DEFAULT,
        ?string $disk = null,
        array $meta = []
    ): Collection {
        return $this->mediaService()->update($model, $files, $collection, $disk, $meta);
    }

    /**
     * Delete media item(s) by ID, Array of IDs, or Media model.
     */
    protected function deleteMedia(mixed $ids): bool
    {
        return $this->mediaService()->delete($ids);
    }

    /**
     * Upload or replace user/model avatar.
     */
    protected function uploadAvatar(Model $model, UploadedFile $file, ?string $disk = null)
    {
        return $this->mediaService()->uploadAvatar($model, $file, $disk);
    }

    /**
     * Upload or replace user/model cover photo.
     */
    protected function uploadCoverPhoto(Model $model, UploadedFile $file, ?string $disk = null)
    {
        return $this->mediaService()->uploadCoverPhoto($model, $file, $disk);
    }

    /**
     * Upload or replace thumbnail image.
     */
    protected function uploadThumbnail(Model $model, UploadedFile $file, ?string $disk = null)
    {
        return $this->mediaService()->uploadThumbnail($model, $file, $disk);
    }

    /**
     * Upload multiple gallery images.
     */
    protected function uploadGallery(Model $model, array $files, ?string $disk = null): Collection
    {
        return $this->mediaService()->uploadGallery($model, $files, $disk);
    }

    /**
     * Upload document/file attachment.
     */
    protected function uploadDocument(Model $model, UploadedFile $file, ?string $disk = null)
    {
        return $this->mediaService()->uploadDocument($model, $file, $disk);
    }
}
