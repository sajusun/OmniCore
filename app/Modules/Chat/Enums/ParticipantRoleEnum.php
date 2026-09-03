<?php

namespace App\Modules\Chat\Enums;

enum ParticipantRoleEnum: string
{
    case OWNER  = 'owner';
    case ADMIN  = 'admin';
    case MEMBER = 'member';
}
