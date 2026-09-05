<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class FileService
{
    /**
     * Upload a file to storage (local, public, S3, etc.).
     *
     * @param UploadedFile|mixed $file
     * @param string $folder Target folder (e.g. 'users/avatars')
     * @param string|null $disk Storage disk (defaults to config filesystems.default)
     * @param string|null $name Optional filename prefix
     * @return string Relative stored file path
     */
    public function upload(mixed $file, string $folder, ?string $disk = null, ?string $name = null): ?string
    {
        if (!$file instanceof UploadedFile && (!is_object($file) || !method_exists($file, 'isValid') || !$file->isValid())) {
            return null;
        }

        $disk ??= config('filesystems.default', 'public');
        $folder = trim($folder, '/\\');

        $extension = method_exists($file, 'getClientOriginalExtension') && $file->getClientOriginalExtension()
            ? $file->getClientOriginalExtension()
            : ($file->guessExtension() ?: 'bin');

        $fileName = ($name ? Str::slug($name) . '-' : '') . Str::uuid() . '.' . $extension;

        $options = [];
        if ($disk === 's3' || config('filesystems.default') === 's3') {
            $options['CacheControl'] = 'public, max-age=31536000';
        }

        return Storage::disk($disk)->putFileAs($folder, $file, $fileName, $options);
    }

    /**
     * Delete a file from storage. Handles full URLs, S3 bucket prefixes, and legacy paths.
     *
     * @param string|null $path File path or URL
     * @param string|null $disk Storage disk
     * @return bool
     */
    public function delete(?string $path, ?string $disk = null): bool
    {
        $disk ??= config('filesystems.default', 'public');

        if (!$path) {
            return false;
        }

        try {
            // Strip scheme & domain if full URL is passed
            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                $parsed = parse_url($path);
                $path = $parsed['path'] ?? '';
            }

            $path = ltrim($path, '/');
            $path = preg_replace('#^storage/#', '', $path);

            $bucket = config("filesystems.disks.{$disk}.bucket");
            if ($bucket) {
                $path = preg_replace('#^' . preg_quote($bucket, '#') . '/#', '', $path);
            }

            if (!$path) {
                return false;
            }

            // 1. Storage disk deletion
            if (Storage::disk($disk)->exists($path)) {
                return Storage::disk($disk)->delete($path);
            }

            if ($disk !== 'public' && Storage::disk('public')->exists($path)) {
                return Storage::disk('public')->delete($path);
            }

            // 2. Legacy physical public file fallback
            $publicFullPath = public_path($path);
            if (file_exists($publicFullPath) && is_file($publicFullPath)) {
                return @unlink($publicFullPath);
            }

            return false;
        } catch (Throwable $e) {
            Log::warning("FileService delete failed for [{$path}]: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Replace an existing file with a new one.
     *
     * @param mixed $file New uploaded file
     * @param string|null $oldPath Old file path/URL to delete
     * @param string $folder Target directory
     * @param string|null $disk Storage disk
     * @param string|null $name Optional filename prefix
     * @return string|null New stored path
     */
    public function replace(mixed $file, ?string $oldPath, string $folder, ?string $disk = null, ?string $name = null): ?string
    {
        $disk ??= config('filesystems.default', 'public');

        if ($oldPath) {
            $this->delete($oldPath, $disk);
        }

        return $this->upload($file, $folder, $disk, $name);
    }

    /**
     * Get accessible URL for a stored file across any storage disk.
     *
     * @param string|null $path
     * @param string|null $disk
     * @return string|null
     */
    public function url(?string $path, ?string $disk = null): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        $disk ??= config('filesystems.default', 'public');
        $cleanPath = ltrim(preg_replace('#^(storage/|public/|/storage/|/public/)#', '', $path), '/\\');

        if (Storage::disk($disk)->exists($cleanPath)) {
            return Storage::disk($disk)->url($cleanPath);
        }

        return asset($path);
    }
}