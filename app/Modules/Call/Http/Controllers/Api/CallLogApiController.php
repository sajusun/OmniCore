<?php

namespace App\Modules\Call\Http\Controllers\Api;

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

        return $this->paginated(
            $logs,
            CallLogResource::class,
            'Call history retrieved successfully'
        );
    }

    /**
     * Get missed calls.
     */
    public function missed(): JsonResponse
    {
        $user = auth('api')->user();
        $logs = $this->logService->missed($user);

        return $this->paginated(
            $logs,
            CallLogResource::class,
            'Missed calls retrieved successfully'
        );
    }

    /**
     * Show single call session details.
     */
    public function show(CallSession $callSession): JsonResponse
    {
        $session = $this->logService->show($callSession);

        return $this->success(
            new CallSessionResource($session),
            'Call session retrieved successfully'
        );
    }

    /**
     * Delete call log.
     */
    public function destroy(CallSession $callSession): JsonResponse
    {
        $user = auth('api')->user();
        $this->logService->delete($callSession, $user);

        return $this->success(null, 'Call log deleted successfully');
    }
}
