<?php

namespace App\Modules\Chat\Enums;

enum ChatRoomTypeEnum: string
{
    case SINGLE  = 'single';
    case GROUP   = 'group';
    case CHANNEL = 'channel';
}
