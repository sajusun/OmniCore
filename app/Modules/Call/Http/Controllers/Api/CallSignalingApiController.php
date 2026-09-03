<?php

namespace App\Modules\Call\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Modules\Call\Http\Requests\InitiateCallRequest;
use App\Modules\Call\Http\Requests\SignalingRequest;
use App\Modules\Call\Http\Requests\TrackStateRequest;
use App\Modules\Call\Http\Resources\CallSessionResource;
use App\Modules\Call\Models\CallSession;
use App\Modules\Call\Services\CallSignalingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CallSignalingApiController extends Controller
{
    public function __construct(protected CallSignalingService $signalingService)
    {
        parent::__construct();
    }

    /**
     * Initiate a new audio/video/screen-share call.
     */
    public function initiate(InitiateCallRequest $request): JsonResponse
    {
        $caller = auth('api')->user();
        $session = $this->signalingService->initiateCall($caller, $request->validated());

        return Helper::jsonResponse(
            true,
            'Call initiated successfully',
            201,
            new CallSessionResource($session)
        );
    }

    /**
     * Acknowledge device ringing.
     */
    public function ring(CallSession $callSession): JsonResponse
    {
        $user = auth('api')->user();
        $this->signalingService->ring($callSession, $user);

        return Helper::jsonResponse(true, 'Ringing status reported successfully', 200);
    }

    /**
     * Accept incoming call.
     */
    public function accept(CallSession $callSession): JsonResponse
    {
        $user = auth('api')->user();
        $session = $this->signalingService->accept($callSession, $user);

        return Helper::jsonResponse(
            true,
            'Call accepted successfully',
            200,
            new CallSessionResource($session)
        );
    }

    /**
     * Decline/reject incoming call.
     */
    public function reject(CallSession $callSession, Request $request): JsonResponse
    {
        $user = auth('api')->user();
        $reason = $request->input('reason');
        $this->signalingService->reject($callSession, $user, $reason);

        return Helper::jsonResponse(true, 'Call declined successfully', 200);
    }

    /**
     * End active call session.
     */
    public function end(CallSession $callSession): JsonResponse
    {
        $user = auth('api')->user();
        $session = $this->signalingService->end($callSession, $user);

        return Helper::jsonResponse(
            true,
            'Call ended successfully',
            200,
            new CallSessionResource($session)
        );
    }

    /**
     * Report user busy state.
     */
    public function busy(CallSession $callSession): JsonResponse
    {
        $user = auth('api')->user();
        $this->signalingService->busy($callSession, $user);

        return Helper::jsonResponse(true, 'Busy state reported successfully', 200);
    }

    /**
     * WebRTC SDP Offer/Answer or ICE Candidate signaling relay.
     */
    public function signal(CallSession $callSession, SignalingRequest $request): JsonResponse
    {
        $user = auth('api')->user();
        $this->signalingService->signal(
            $callSession,
            $user,
            $request->validated('type'),
            $request->validated('payload'),
            $request->validated('target_id')
        );

        return Helper::jsonResponse(true, 'Signal relayed successfully', 200);
    }

    /**
     * Toggle mic, camera, or screen share track states.
     */
    public function trackState(CallSession $callSession, TrackStateRequest $request): JsonResponse
    {
        $user = auth('api')->user();
        $this->signalingService->trackState(
            $callSession,
            $user,
            (bool) $request->validated('is_muted'),
            (bool) $request->validated('is_video_enabled'),
            (bool) $request->validated('is_screen_sharing')
        );

        return Helper::jsonResponse(true, 'Track state updated successfully', 200);
    }
}
