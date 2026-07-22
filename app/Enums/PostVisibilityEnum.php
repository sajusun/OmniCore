<?php
namespace App\Enums;

enum PostVisibilityEnum: string
{
    case PUBLIC = 'public';
    case FOLLOWERS = 'followers';
    case FRIENDS = 'friends';
    case PRIVATE = 'private';
}