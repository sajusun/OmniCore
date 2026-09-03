<?php

namespace App\Modules\Media\Services;

use App\Modules\Media\Enums\MediaCollection;
use App\Modules\Media\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaService
{
    /**
     * Upload single or multiple files for a model.
     *
     * @param Model $model
     * @param UploadedFile|array $files
     * @param string $collection
     * @param string|null $disk
     * @param array $meta
     * @param bool $isPrimary
     * @return Collection<Media>
     */
    public function upload(
        Model $model,
        mixed $files,
        string $collection = MediaCollection::DEFAULT,
        ?string $disk = null,
        array $meta = [],
        bool $isPrimary = false
    ): Collection {
        $disk = $disk ?? config('filesystems.default', 'public');
        $files = is_array($files) ? $files : [$files];
        $uploadedMedia = [];

        foreach ($files as $file) {
            if (!$file instanceof UploadedFile || !$file->isValid()) {
                continue;
            }

            // Generate clean, collision-free filename
            $extension = $file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'bin';
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $cleanName = Str::slug($originalName) ?: 'media';
            $fileName = $cleanName . '_' . time() . '_' . Str::random(6) . '.' . $extension;

            // Store using storeAs for predictable pathing
            $path = $file->storeAs($collection, $fileName, $disk);

            // Extract dimensions if image
            $width = null;
            $height = null;
            $mimeType = $file->getMimeType();
            if (str_starts_with($mimeType, 'image/') && function_exists('getimagesize')) {
                $imageSize = @getimagesize($file->getRealPath());
                if ($imageSize) {
                    $width = $imageSize[0] ?? null;
                    $height = $imageSize[1] ?? null;
                }
            }

            // Save clean relative path (never prepend 'storage/')
            $media = $model->media()->create([
                'disk'            => $disk,
                'collection_name' => $collection,
                'original_name'   => $file->getClientOriginalName(),
                'file_name'       => $fileName,
                'mime_type'       => $mimeType,
                'extension'       => $extension,
                'size'            => $file->getSize(),
                'path'            => $path,
                'width'           => $width,
                'height'          => $height,
                'meta'            => $meta,
                'is_primary'      => $isPrimary,
                'status'          => 'active',
            ]);

            $uploadedMedia[] = $media;
        }

        return collect($uploadedMedia);
    }

    /**
     * Replace existing media in a collection with new file(s).
     */
    public function update(
        Model $model,
        mixed $files,
        string $collection = MediaCollection::DEFAULT,
        ?string $disk = null,
        array $meta = []
    ): Collection {
        // Automatically delete previous media in this collection
        $this->clearCollection($model, $collection);

        return $this->upload($model, $files, $collection, $disk, $meta);
    }

    /**
     * Delete media items by ID, Array of IDs, or Media Model instance.
     */
    public function delete(mixed $ids): bool
    {
        if ($ids instanceof Media) {
            return $ids->delete();
        }

        $ids = is_array($ids) ? $ids : [$ids];
        $mediaItems = Media::whereIn('id', $ids)->get();

        if ($mediaItems->isEmpty()) {
            return false;
        }

        foreach ($mediaItems as $media) {
            $media->delete(); // Model booted event deletes physical file automatically
        }

        return true;
    }

    /**
     * Clear all media from a specific collection on a model.
     */
    public function clearCollection(Model $model, string $collection): void
    {
        $existingMedia = $model->media()->where('collection_name', $collection)->get();
        foreach ($existingMedia as $media) {
            $media->delete();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Dedicated Convenience Helpers (Avatar, Cover, Gallery, etc.)
    |--------------------------------------------------------------------------
    */

    /**
     * Upload or update user/model avatar.
     */
    public function uploadAvatar(Model $model, UploadedFile $file, ?string $disk = null): ?Media
    {
        return $this->update($model, $file, MediaCollection::AVATAR, $disk)->first();
    }

    /**
     * Upload or update user/model cover photo.
     */
    public function uploadCoverPhoto(Model $model, UploadedFile $file, ?string $disk = null): ?Media
    {
        return $this->update($model, $file, MediaCollection::COVER_PHOTO, $disk)->first();
    }

    /**
     * Upload or update thumbnail image.
     */
    public function uploadThumbnail(Model $model, UploadedFile $file, ?string $disk = null): ?Media
    {
        return $this->update($model, $file, MediaCollection::THUMBNAIL, $disk)->first();
    }

    /**
     * Upload multiple gallery images.
     */
    public function uploadGallery(Model $model, array $files, ?string $disk = null): Collection
    {
        return $this->upload($model, $files, MediaCollection::GALLERY, $disk);
    }

    /**
     * Upload document/file attachment.
     */
    public function uploadDocument(Model $model, UploadedFile $file, ?string $disk = null): ?Media
    {
        return $this->upload($model, $file, MediaCollection::DOCUMENT, $disk)->first();
    }
}
