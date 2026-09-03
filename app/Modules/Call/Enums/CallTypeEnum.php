<?php

namespace App\Modules\Call\Enums;

enum CallTypeEnum: string
{
    case AUDIO        = 'audio';
    case VIDEO        = 'video';
    case SCREEN_SHARE = 'screen_share';
}
