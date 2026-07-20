<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileService
{
    public function upload(UploadedFile $file, string $folder, string $disk = 'public', ?string $name = null): string {
        $fileName = ($name ? Str::slug($name) . '-' : '') . Str::uuid() . '.' . $file->getClientOriginalExtension();

        return Storage::disk($disk)->putFileAs($folder, $file, $fileName);
    }

    public function delete(?string $path, string $disk = 'public'): bool {
        if (!$path || !Storage::disk($disk)->exists($path)) {
            return false;
        }

        return Storage::disk($disk)->delete($path);
    }

    public function replace(UploadedFile $file, ?string $oldPath, string $folder, string $disk = 'public', ?string $name = null): string {
        $this->delete($oldPath, $disk);

        return $this->upload($file, $folder, $disk, $name);
    }

    public function url(?string $path, string $disk = 'public'): ?string {
        return $path ? Storage::disk($disk)->url($path) : null;
    }
}