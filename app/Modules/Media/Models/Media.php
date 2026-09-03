<?php

namespace App\Modules\Media\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $table = 'media';

    protected $fillable = [
        'disk',
        'collection_name',
        'original_name',
        'file_name',
        'mime_type',
        'extension',
        'size',
        'path',
        'url',
        'width',
        'height',
        'meta',
        'sort_order',
        'is_primary',
        'status',
    ];

    protected $casts = [
        'meta'       => 'array',
        'is_primary' => 'boolean',
        'size'       => 'integer',
        'sort_order' => 'integer',
        'width'      => 'integer',
        'height'     => 'integer',
    ];

    protected $hidden = [
        'mediable_type',
        'mediable_id',
        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'full_url',
        'human_readable_size',
    ];

    /**
     * Boot model and hook physical file deletion on delete.
     */
    protected static function booted()
    {
        static::deleting(function (Media $media) {
            if ($media->path) {
                try {
                    $disk = $media->disk ?? config('filesystems.default', 'public');
                    // Remove any legacy 'storage/' prefix if present
                    $cleanPath = preg_replace('/^storage\//', '', $media->path);
                    Storage::disk($disk)->delete($cleanPath);
                } catch (\Throwable $e) {
                    // Log or ignore storage deletion errors to allow DB record cleanup
                }
            }
        });
    }

    /**
     * Polymorphic Relationship to parent model.
     */
    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Dynamic URL resolution based on assigned storage disk.
     */
    public function getFullUrlAttribute(): ?string
    {
        if (!$this->path) {
            return $this->url ?? null;
        }

        // Return directly if it's already an absolute URL
        if (filter_var($this->path, FILTER_VALIDATE_URL)) {
            return $this->path;
        }

        try {
            $disk = $this->disk ?? config('filesystems.default', 'public');
            $cleanPath = preg_replace('/^storage\//', '', $this->path);

            return Storage::disk($disk)->url($cleanPath);
        } catch (\Throwable $e) {
            return asset($this->path);
        }
    }

    /**
     * Get temporary signed URL for private cloud buckets (e.g. S3 private documents).
     */
    public function getTemporaryUrl(int $minutes = 30): ?string
    {
        if (!$this->path) {
            return null;
        }

        try {
            $disk = $this->disk ?? config('filesystems.default', 'public');
            $cleanPath = preg_replace('/^storage\//', '', $this->path);

            return Storage::disk($disk)->temporaryUrl($cleanPath, now()->addMinutes($minutes));
        } catch (\Throwable $e) {
            return $this->full_url;
        }
    }

    /**
     * Get human-readable file size (e.g. 1.5 MB, 450 KB).
     */
    public function getHumanReadableSizeAttribute(): string
    {
        $bytes = $this->size ?? 0;
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = floor(log($bytes, 1024));

        return round($bytes / pow(1024, $power), 2) . ' ' . ($units[$power] ?? 'B');
    }
}
