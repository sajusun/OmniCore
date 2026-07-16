<?php

namespace App\Modules\Media\Traits;

use App\Modules\Media\Models\Media;
use Illuminate\Support\Facades\Storage;

trait HandlesMedia
{

    protected function uploadMedia($model, $files, $collection = 'default', $disk = 'public')
    {

        $files = is_array($files) ? $files : [$files];
        $uploadedMedia = [];
        foreach ($files as $file) {
            if (!$file) {
                continue;
            }
            $path = $file->store($collection, $disk);

            $media = $model->media()->create([

                'disk' => $disk,
                'collection_name' => $collection,
                'original_name' => $file->getClientOriginalName(),
                'file_name' => basename($path),
                'mime_type' => $file->getMimeType(),
                'extension' => $file->getClientOriginalExtension(),
                'size' => $file->getSize(),
                'path' => 'storage/' . $path,
                'url' => asset('storage/' . $path),
                'status' => 'active',
            ]);

            $uploadedMedia[] = $media;
        }

        return collect($uploadedMedia);
    }


    protected function updateMedia(mixed $model, mixed $files, $collection = 'default', $disk = 'public')
    {

        $existingMedia = $model->media()->where('collection_name', $collection)->get();

        if ($existingMedia->isNotEmpty()) {
            $this->deleteMedia($existingMedia->pluck('id')->toArray());
        }

        return $this->uploadMedia($model, $files, $collection, $disk);
    }


    protected function deleteMedia(int|array $ids): bool
    {

        $ids = is_array($ids) ? $ids : [$ids];

        $mediaItems = Media::whereIn('id', $ids)->get();

        if ($mediaItems->isEmpty()) {
            return false;
        }

        foreach ($mediaItems as $media) {
            if ($media->path) {
                $storagePath = str_replace('storage/', '', $media->path);
                Storage::disk($media->disk)->delete($storagePath);
            }

            $media->delete();
        }

        return true;
    }
}
