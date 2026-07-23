<?php

namespace App\Enums\Chat;

enum ParticipantRoleEnum: string
{
    case OWNER = 'owner';
    case ADMIN = 'admin';
    case MEMBER = 'member';
}
