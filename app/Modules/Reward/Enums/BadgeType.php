<?php

namespace App\Modules\Reward\Enums;

enum BadgeType: string
{
    case ACHIEVEMENT = 'achievement';
    case STREAK = 'streak';
    case MILESTONE = 'milestone';
    case SPENDING = 'spending';
}
