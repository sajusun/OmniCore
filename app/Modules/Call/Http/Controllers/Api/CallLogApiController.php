<?php

namespace App\Modules\Call\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Modules\Call\Http\Resources\CallLogResource;
use App\Modules\Call\Http\Resources\CallSessionResource;
use App\Modules\Call\Models\CallSession;
use App\Modules\Call\Services\CallLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CallLogApiController extends Controller
{
    public function __construct(protected CallLogService $logService)
    {
        parent::__construct();
    }

    /**
     * Get user call logs.
     */
    public function history(Request $request): JsonResponse
    {
        $user = auth('api')->user();
        $logs = $this->logService->history($user, $request->query('type'), $request->query('status'));

        return Helper::jsonResponse(
            true,
            'Call history retrieved successfully',
            200,
            CallLogResource::collection($logs),
            [
                'current_page' => $logs->currentPage(),
                'last_page'    => $logs->lastPage(),
                'total'        => $logs->total(),
            ]
        );
    }

    /**
     * Get missed calls.
     */
    public function missed(): JsonResponse
    {
        $user = auth('api')->user();
        $logs = $this->logService->missed($user);

        return Helper::jsonResponse(
            true,
            'Missed calls retrieved successfully',
            200,
            CallLogResource::collection($logs),
            [
                'current_page' => $logs->currentPage(),
                'last_page'    => $logs->lastPage(),
                'total'        => $logs->total(),
            ]
        );
    }

    /**
     * Show single call session details.
     */
    public function show(CallSession $callSession): JsonResponse
    {
        $session = $this->logService->show($callSession);

        return Helper::jsonResponse(
            true,
            'Call session retrieved successfully',
            200,
            new CallSessionResource($session)
        );
    }

    /**
     * Delete call log.
     */
    public function destroy(CallSession $callSession): JsonResponse
    {
        $user = auth('api')->user();
        $this->logService->delete($callSession, $user);

        return Helper::jsonResponse(true, 'Call log deleted successfully', 200);
    }
}
