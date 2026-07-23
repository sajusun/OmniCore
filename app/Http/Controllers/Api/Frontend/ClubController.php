<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Models\Club;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Services\ClubService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClubResource;
use App\Http\Requests\StoreClubRequest;
use App\Http\Requests\UpdateClubRequest;

class ClubController extends Controller
{
    protected ClubService $clubService;

    public function __construct(ClubService $clubService)
    {
        parent::__construct();
        $this->clubService = $clubService;
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
}
