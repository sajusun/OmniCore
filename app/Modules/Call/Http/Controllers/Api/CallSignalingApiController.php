<?php

namespace App\Modules\Call\Http\Controllers\Api;

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

        return $this->created(
            new CallSessionResource($session),
            'Call initiated successfully'
        );
    }

    /**
     * Acknowledge device ringing.
     */
    public function ring(CallSession $callSession): JsonResponse
    {
        $user = auth('api')->user();
        $this->signalingService->ring($callSession, $user);

        return $this->success(null, 'Ringing status reported successfully');
    }

    /**
     * Accept incoming call.
     */
    public function accept(CallSession $callSession): JsonResponse
    {
        $user = auth('api')->user();
        $session = $this->signalingService->accept($callSession, $user);

        return $this->success(
            new CallSessionResource($session),
            'Call accepted successfully'
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

        return $this->success(null, 'Call declined successfully');
    }

    /**
     * End active call session.
     */
    public function end(CallSession $callSession): JsonResponse
    {
        $user = auth('api')->user();
        $session = $this->signalingService->end($callSession, $user);

        return $this->success(
            new CallSessionResource($session),
            'Call ended successfully'
        );
    }

    /**
     * Report user busy state.
     */
    public function busy(CallSession $callSession): JsonResponse
    {
        $user = auth('api')->user();
        $this->signalingService->busy($callSession, $user);

        return $this->success(null, 'Busy state reported successfully');
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

        return $this->success(null, 'Signal relayed successfully');
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

        return $this->success(null, 'Track state updated successfully');
    }
}
