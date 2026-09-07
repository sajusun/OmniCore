<?php

namespace App\Modules\Interaction\Http\Controllers\Api;

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

            $recorded = $this->viewService->recordView(
                $model,
                $request->user('api'),
                $request->ip(),
                (int) $request->input('cooldown_minutes', 60),
                $request->userAgent()
            );

            $result = [
                'recorded' => $recorded,
                'views_count' => method_exists($model, 'viewsCount') ? $model->viewsCount() : ($model->views_count ?? 0),
            ];

            $message = $recorded ? 'View recorded successfully.' : 'View already counted recently (cooldown active).';

            return $this->success($result, $message);
        } catch (Exception $e) {
            return $this->error($e->getMessage(), null, 400);
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
            'days' => ['nullable', 'integer', 'min:1', 'max:90'],
        ]);

        try {
            $model = $this->viewService->resolveModel(
                $request->input('subject_type'),
                $request->input('subject_id')
            );

            $stats = $this->viewService->getDailyStats($model, (int) $request->input('days', 7));

            return $this->success($stats, 'View stats retrieved successfully.');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), null, 400);
        }
    }
}
