<?php

namespace App\Enums;

enum PageName: string
{
    case HOME           = 'home';
    case ABOUT          = 'about';
    case CONTACT        = 'contact';
        // case SERVICES       = 'services';
    case PRIVACY_POLICY = 'privacy-policy';
    case TERMS          = 'terms-and-conditions';

    public function label(): string
    {
        return match ($this) {
            self::HOME           => 'Home',
            self::ABOUT          => 'About Us',
            self::CONTACT        => 'Contact',
            // self::SERVICES       => 'Services',
            self::PRIVACY_POLICY => 'Privacy Policy',
            self::TERMS          => 'Terms & Conditions',
        };
    }

    public function sections(): array
    {
        return match ($this) {
            self::HOME           => [
                SectionName::HERO,
                SectionName::FEATURES,
                SectionName::TESTIMONIALS,
            ],
            self::ABOUT          => [
                SectionName::HERO,
                SectionName::TEAM,
                SectionName::MISSION,
            ],
            self::CONTACT          => [
                SectionName::HERO_IMAGE,
                SectionName::CONTECT_INFO,
            ],
            self::PRIVACY_POLICY          => [
                SectionName::CONTENT,
            ],
            self::TERMS          => [
                SectionName::CONTENT,
            ],
        };
    }
}
