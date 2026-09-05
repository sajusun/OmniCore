<?php

namespace App\Modules\Interaction\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Modules\Interaction\Http\Requests\RecordViewRequest;
use App\Modules\Interaction\Services\ViewService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ViewApiController extends Controller
{
    public function __construct(
        protected ViewService $viewService
    ) {}

    /**
     * Record a view/impression for any model (public or authenticated).
     */
    public function store(RecordViewRequest $request): JsonResponse
    {
        try {
            $model = $this->viewService->resolveModel(
                $request->input('subject_type'),
                $request->input('subject_id')
            );

            $result = $this->viewService->recordView(
                $model,
                $request->user('api'),
                $request->ip(),
                $request->userAgent(),
                (int) $request->input('cooldown_minutes', 60)
            );

            $message = $result['recorded'] ? 'View recorded successfully.' : 'View already counted recently (cooldown active).';

            return Helper::jsonResponse(true, $message, 200, $result);
        } catch (Exception $e) {
            return Helper::jsonErrorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Get view stats for a model.
     */
    public function stats(Request $request): JsonResponse
    {
        $request->validate([
            'subject_type' => ['required', 'string'],
            'subject_id' => ['required'],
        ]);

        try {
            $model = $this->viewService->resolveModel(
                $request->input('subject_type'),
                $request->input('subject_id')
            );

            $stats = $this->viewService->getViewStats($model);

            return Helper::jsonResponse(true, 'View stats retrieved successfully.', 200, $stats);
        } catch (Exception $e) {
            return Helper::jsonErrorResponse($e->getMessage(), 400);
        }
    }
}
