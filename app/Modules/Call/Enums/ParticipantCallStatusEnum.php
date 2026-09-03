<?php

namespace App\Modules\Call\Enums;

enum ParticipantCallStatusEnum: string
{
    case CALLING   = 'calling';
    case RINGING   = 'ringing';
    case CONNECTED = 'connected';
    case REJECTED  = 'rejected';
    case LEFT      = 'left';
    case BUSY      = 'busy';
}
