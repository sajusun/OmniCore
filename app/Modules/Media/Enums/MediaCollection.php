<?php

namespace App\Modules\Media\Enums;

class MediaCollection
{
    public const AVATAR      = 'avatar';
    public const COVER_PHOTO = 'cover_photo';
    public const THUMBNAIL   = 'thumbnail';
    public const GALLERY     = 'gallery';
    public const BANNER      = 'banner';
    public const DOCUMENT    = 'document';
    public const FILE        = 'file';
    public const VIDEO       = 'video';
    public const AUDIO       = 'audio';
    public const DEFAULT     = 'default';

    /**
     * Get all predefined collection names.
     */
    public static function all(): array
    {
        return [
            self::AVATAR,
            self::COVER_PHOTO,
            self::THUMBNAIL,
            self::GALLERY,
            self::BANNER,
            self::DOCUMENT,
            self::FILE,
            self::VIDEO,
            self::AUDIO,
            self::DEFAULT,
        ];
    }
}
