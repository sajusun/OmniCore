<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Models\Club;
use App\Models\User;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Services\ClubService;
use App\Services\EventService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClubResource;
use App\Http\Requests\StoreClubRequest;
use App\Http\Requests\UpdateClubRequest;

class ClubController extends Controller
{
    protected ClubService $clubService;
    protected EventService $eventService;
    protected User $user;

    public function __construct(ClubService $clubService, EventService $eventService)
    {
        parent::__construct();
        $this->clubService = $clubService;
        $this->user = auth('api')->user();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['type', 'country', 'state', 'city', 'status', 'created_by', 'search']);
        $perPage = $request->query('per_page', 15);

        $clubs = $this->clubService->list($filters, $perPage);

        return Helper::jsonResponse(true, 'Clubs retrieved successfully', 200, ClubResource::collection($clubs), true, $clubs);
    }

    public function myClub(Request $request): JsonResponse
    {
        $filters = $request->only(['type', 'country', 'state', 'city', 'status', 'created_by', 'search']);
        $perPage = $request->query('per_page', 15);

        $clubs = $this->clubService->list($filters, $perPage, auth('api')->user());

        return Helper::jsonResponse(true, 'Clubs retrieved successfully', 200, ClubResource::collection($clubs), true, $clubs);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClubRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail');
        }
        if ($request->hasFile('images')) {
            $data['images'] = $request->file('images');
        }
        if ($request->hasFile('video')) {
            $data['video'] = $request->file('video');
        }
        if ($request->hasFile('videos')) {
            $data['videos'] = $request->file('videos');
        }

        $club = $this->clubService->store($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Club created successfully!',
            'data' => new ClubResource($club->load(['media', 'creator']))
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $club = $this->clubService->find($id);
        $clubAdmin = $club->creator;
        $events = $clubAdmin->events;
        $club->events = $events;

        return $this->success(new ClubResource($club), 'Club retrieved successfully', 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClubRequest $request, Club $club): JsonResponse
    {
        if ($club->created_by !== auth('api')->id()) {
            return $this->error('You do not have permission to update this club.', null, 403);
        }

        $data = $request->validated();

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail');
        }
        if ($request->hasFile('images')) {
            $data['images'] = $request->file('images');
        }
        if ($request->hasFile('video')) {
            $data['video'] = $request->file('video');
        }
        if ($request->hasFile('videos')) {
            $data['videos'] = $request->file('videos');
        }

        $updatedClub = $this->clubService->update($club, $data);

        return $this->success(new ClubResource($updatedClub->load(['media', 'creator'])), 'Club updated successfully!', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Club $club): JsonResponse
    {
        if ($club->created_by !== auth('api')->id()) {
            return $this->error('You do not have permission to delete this club.', null, 403);
        }

        $result = $this->clubService->delete($club);

        if ($result) {
            return $this->success([], 'Club successfully deleted!', 200);
        }

        return $this->error('Failed to delete club.', null, 500);
    }

    // ── Membership Endpoints ─────────────────────────────────────────────

    /**
     * Join a club.
     * POST /clubs/{club}/join
     */
    public function join(Club $club): JsonResponse
    {
        try {
            $membership = $this->clubService->join($club, auth('api')->user());

            return $this->success([
                'role'    => $membership->role,
                'status'  => $membership->status,
                'message' => $membership->status === 'approved'
                    ? 'You have joined the club successfully!'
                    : 'Your join request is pending approval.',
            ], 'Join request processed.', 201);
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), null, 422);
        }
    }

    /**
     * Leave a club.
     * DELETE /clubs/{club}/leave
     */
    public function leave(Club $club): JsonResponse
    {
        try {
            $this->clubService->leave($club, auth('api')->user());
            return $this->success([], 'You have left the club.', 200);
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), null, 422);
        }
    }

    /**
     * List approved members.
     * GET /clubs/{club}/members
     */
    public function members(Club $club, Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 15);
        $members = $this->clubService->members($club, $perPage);

        return $this->response(
            status: true,
            message: 'Members retrieved successfully',
            code: 200,
            data: $members->map(fn($m) => [
                'user_id'    => $m->user_id,
                'name'       => $m->user?->name,
                'avatar'     => $m->user?->avatar ?? null,
                'role'       => $m->role,
                'joined_at'  => $m->joined_at?->toIso8601String(),
            ]),
            paginate: true,
            paginateData: $members
        );
    }

    /**
     * Approve a pending member request.
     * POST /clubs/{club}/members/{user}/approve
     * Future use — currently all joins are auto-approved.
     */
    public function approveMember(Club $club, \App\Models\User $user): JsonResponse
    {
        try {
            $this->clubService->approveMember($club, $user, auth('api')->user());
            return $this->success([], 'Member approved successfully.', 200);
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), null, 422);
        }
    }

    /**
     * Reject a pending member request.
     * POST /clubs/{club}/members/{user}/reject
     * Future use.
     */
    public function rejectMember(Club $club, \App\Models\User $user): JsonResponse
    {
        try {
            $this->clubService->rejectMember($club, $user, auth('api')->user());
            return $this->success([], 'Member rejected.', 200);
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), null, 422);
        }
    }

    /**
     * Admin removes a member forcefully.
     * DELETE /clubs/{club}/members/{user}
     */
    public function removeMember(Club $club, \App\Models\User $user): JsonResponse
    {
        try {
            $this->clubService->removeMember($club, $user, auth('api')->user());
            return $this->success([], 'Member removed successfully.', 200);
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), null, 422);
        }
    }
}
