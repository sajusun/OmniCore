<?php

namespace App\Modules\CMS\Enums;

enum PageName: string
{
    case CONTACT        = 'help';
    case PRIVACY_POLICY = 'privacy-policy';
    case TERMS          = 'terms-and-conditions';

    public function label(): string
    {
        return match ($this) {
            self::CONTACT        => 'Help',
            self::PRIVACY_POLICY => 'Privacy Policy',
            self::TERMS          => 'Terms & Conditions',
        };
    }

    public function sections(): array
    {
        return match ($this) {
            self::CONTACT          => [
                SectionName::CONTENT,
            ],
            self::PRIVACY_POLICY   => [
                SectionName::CONTENT,
            ],
            self::TERMS            => [
                SectionName::CONTENT,
            ],
        };
    }
}
