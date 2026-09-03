<?php

namespace App\Modules\AppSupport\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\AppSupport\Enums\SupportCategory;
use App\Modules\AppSupport\Http\Requests\CreateSupportRequest;
use App\Modules\AppSupport\Services\AppSupportService;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppSupportApiController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected AppSupportService $supportService
    ) {}

    /**
     * Submit an app issue / support report with optional screenshots.
     *
     * @param CreateSupportRequest $request
     * @return JsonResponse
     */
    public function store(CreateSupportRequest $request): JsonResponse
    {
        try {
            $user = auth('api')->user() ?? auth()->user();

            if (!$user) {
                return $this->error('Unauthorized', null, 401);
            }

            $attachments = $request->file('attachments');

            $support = $this->supportService->createReport(
                user: $user,
                data: $request->validated(),
                attachments: $attachments
            );

            return $this->success(
                data: $support,
                message: 'Your report has been submitted successfully! We have sent a confirmation to your email.',
                status: 201
            );
        } catch (Exception $e) {
            return $this->error(
                message: 'Failed to submit report. Please try again.',
                errors: $e->getMessage(),
                status: 500
            );
        }
    }

    /**
     * List authenticated user's submitted reports.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $userId = auth('api')->id() ?? auth()->id();

        if (!$userId) {
            return $this->error('Unauthorized', null, 401);
        }

        $perPage = (int) $request->get('per_page', 15);
        $reports = $this->supportService->getUserReports($userId, $perPage);

        return $this->success(
            data: $reports,
            message: 'Support reports retrieved successfully.'
        );
    }

    /**
     * Get details of a specific report with all replies and media attachments.
     *
     * @param int|string $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $userId = auth('api')->id() ?? auth()->id();

        if (!$userId) {
            return $this->error('Unauthorized', null, 401);
        }

        try {
            $report = $this->supportService->getReportDetail($id, $userId);

            return $this->success(
                data: $report,
                message: 'Support report detail retrieved.'
            );
        } catch (Exception $e) {
            return $this->error('Support report not found.', null, 404);
        }
    }

    /**
     * User submits a reply/follow-up message to an existing support report.
     *
     * @param Request $request
     * @param int|string $id
     * @return JsonResponse
     */
    public function reply(Request $request, $id): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:5000',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:jpeg,png,jpg,webp,gif,pdf|max:10240',
        ]);

        try {
            $user = auth('api')->user() ?? auth()->user();

            if (!$user) {
                return $this->error('Unauthorized', null, 401);
            }

            $report = $this->supportService->getReportDetail($id, $user->id);

            $reply = $this->supportService->sendUserReply(
                support: $report,
                message: $request->input('message'),
                user: $user,
                attachments: $request->file('attachments')
            );

            return $this->success(
                data: $reply,
                message: 'Your reply has been sent successfully.',
                status: 201
            );
        } catch (Exception $e) {
            return $this->error('Failed to send reply. ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Get list of available issue categories for the mobile app dropdown.
     *
     * @return JsonResponse
     */
    public function categories(): JsonResponse
    {
        return $this->success(
            data: SupportCategory::forSelect(),
            message: 'Support categories list.'
        );
    }
}
