<?php

namespace App\Modules\Social\Enums;

enum FriendRequestStatus: string
{
    case Pending  = 'pending';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
}
