<?php

namespace App\Modules\Call\Enums;

enum CallStatusEnum: string
{
    case INITIATING = 'initiating';
    case RINGING    = 'ringing';
    case CONNECTED  = 'connected';
    case ENDED      = 'ended';
    case MISSED     = 'missed';
    case REJECTED   = 'rejected';
    case BUSY       = 'busy';
}
