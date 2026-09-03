<?php

namespace App\Modules\Social\Enums;

enum RelationshipType: string
{
    case SELF           = 'self';
    case FRIEND         = 'friend';
    case REQUEST_SENT   = 'request_sent';
    case REQUEST_RECEIVED = 'request_received';
    case FOLLOWING_ONLY = 'following_only';
    case FOLLOWED_BY_ONLY = 'followed_by_only';
    case MUTUAL_FOLLOW  = 'mutual_follow';
    case BLOCKED        = 'blocked';
    case BLOCKED_BY     = 'blocked_by';
    case NONE           = 'none';
}
