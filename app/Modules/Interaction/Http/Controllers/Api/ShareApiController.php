<?php

namespace App\Modules\Interaction\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Interaction\Http\Requests\GenerateShareLinkRequest;
use App\Modules\Interaction\Http\Resources\ShareLinkResource;
use App\Modules\Interaction\Services\ShareService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;

class ShareApiController extends Controller
{
    public function __construct(
        protected ShareService $shareService
    ) {}

    /**
     * Generate or fetch a share link (public or private).
     */
    public function store(GenerateShareLinkRequest $request): JsonResponse
    {
        try {
            $model = $this->shareService->resolveModel(
                $request->input('subject_type'),
                $request->input('subject_id')
            );

            $type = $request->input('type', 'public');
            $user = $request->user();

            if ($type === 'public') {
                $shareLink = $this->shareService->generatePublicLink($model, $user);
            } else {
                $expiresInMinutes = $request->input('expires_in_minutes');
                $expiresAt = $expiresInMinutes ? Carbon::now()->addMinutes((int) $expiresInMinutes) : null;
                $maxClicks = $request->has('max_clicks') ? (int) $request->input('max_clicks') : ($type === 'single_use' ? 1 : null);

                $shareLink = $this->shareService->generatePrivateLink(
                    $model,
                    $user,
                    $expiresAt,
                    $maxClicks
                );
            }

            return $this->success(
                new ShareLinkResource($shareLink),
                'Share link generated successfully.',
                201
            );
        } catch (Exception $e) {
            return $this->error($e->getMessage(), null, 400);
        }
    }
}
