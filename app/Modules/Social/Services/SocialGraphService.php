<?php

namespace App\Modules\Social\Services;

use App\Models\User;
use App\Services\BaseService;

class SocialGraphService extends BaseService
{
    public function getRelationshipOverview(User $authUser, User $targetUser): array
    {
        return $authUser->getRelationshipWith($targetUser);
    }
}
