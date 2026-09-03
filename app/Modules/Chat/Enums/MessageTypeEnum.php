<?php

namespace App\Modules\Chat\Enums;

enum MessageTypeEnum: string
{
    case TEXT     = 'text';
    case IMAGE    = 'image';
    case VIDEO    = 'video';
    case VOICE    = 'voice';
    case AUDIO    = 'audio';
    case DOCUMENT = 'document';
    case LOCATION = 'location';
    case CONTACT  = 'contact';
    case STICKER  = 'sticker';
    case GIF      = 'gif';
    case SYSTEM   = 'system';
}
