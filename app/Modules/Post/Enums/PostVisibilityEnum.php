<?php

namespace App\Modules\Post\Enums;

enum PostVisibilityEnum: string
{
    case PUBLIC = 'public';
    case FOLLOWERS = 'followers';
    case FRIENDS = 'friends';
    case PRIVATE = 'private';
}
