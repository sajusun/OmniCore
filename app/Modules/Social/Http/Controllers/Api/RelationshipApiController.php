<?php

namespace App\Modules\Social\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Social\Http\Resources\RelationshipStatusResource;
use App\Modules\Social\Services\SocialGraphService;
use Illuminate\Http\JsonResponse;

class RelationshipApiController extends Controller
{
    public function __construct(private readonly SocialGraphService $socialGraphService)
    {
        parent::__construct();
    }

    public function show(User $user): JsonResponse
    {
        $authUser = auth('api')->user() ?? auth()->user();
        $relationship = $this->socialGraphService->getRelationshipOverview($authUser, $user);

        return $this->success(
            new RelationshipStatusResource($relationship),
            'Relationship status fetched successfully.'
        );
    }
}
