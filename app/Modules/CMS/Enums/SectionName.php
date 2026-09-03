<?php

namespace App\Modules\CMS\Enums;

enum SectionName: string
{
    case HERO         = 'hero';
    case HERO_IMAGE   = 'hero-image';
    case FEATURES     = 'features';
    case TESTIMONIALS = 'testimonials';
    case TEAM         = 'team';
    case MISSION      = 'mission';
    case CONTENT      = 'content';
    case CONTECT_INFO = 'contect-info';

    public function elements(): array
    {
        return match ($this) {
            self::HERO_IMAGE   => ['image'],
            self::HERO         => ['title', 'mini-description', 'image'],
            self::FEATURES     => ['title', 'description', 'images'],
            self::TESTIMONIALS => ['title', 'images'],
            self::TEAM         => ['title', 'description', 'images'],
            self::MISSION      => ['title', 'description', 'image', 'meta'],
            self::CONTENT      => ['title', 'description'],
            self::CONTECT_INFO => ['title', 'meta'],
        };
    }
}
