<?php

namespace App\Enums\Chat;

enum ChatRoomTypeEnum: string
{
    case SINGLE = 'single';
    case GROUP = 'group';
    case CHANNEL = 'channel';
}
