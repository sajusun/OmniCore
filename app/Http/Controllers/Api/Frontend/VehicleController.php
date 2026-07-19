<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Helpers\Helper;
use App\Models\Vehicle;
use App\Traits\ApiResponse; // Request rules validator wrapper
use Illuminate\Http\Request;
use App\Services\VehicleService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\VehicleResource;
use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;

class VehicleController extends Controller
{
    protected VehicleService $vehicleService;

    public function __construct(VehicleService $vehicleService)
    {
        $this->vehicleService = $vehicleService;
    }


    public function index(Request $request): JsonResponse
    {
        $vehicles = $this->vehicleService->list($request->user());
        return Helper::jsonResponse('success', 'success', 200, VehicleResource::collection($vehicles), true, $vehicles);
    }


    public function search(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'brand_id', 'status', 'per_page']);
        $results = $this->vehicleService->search($filters);

        return Helper::jsonResponse(true, 'success', 200, VehicleResource::collection($results), true, $results);
    }


    public function store(StoreVehicleRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Multiple file loop format identification checker matrix input processing
        if ($request->hasFile('media')) {
            $data['media'] = $request->file('media');
        }

        $vehicle = $this->vehicleService->create($request->user(), $data);

        return response()->json([
            'status' => 'success',
            'message' => 'Vehicle listed successfully!',
            'data' => $vehicle
        ], 201);
    }


    public function show(int $id): JsonResponse
    {
        $vehicle = $this->vehicleService->find($id);
        return $this->success(new VehicleResource($vehicle), 'Find Data Successfuly', 200);
    }

    public function update(UpdateVehicleRequest $request, Vehicle $vehicle): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('media')) {
            $data['media'] = $request->file('media');
        }

        $updatedVehicle = $this->vehicleService->update($vehicle, $data);

        return $this->success(new VehicleResource($updatedVehicle), 'Vehicle metrics updated successfully!', 200);
    }


    public function destroy(Vehicle $vehicle): JsonResponse
    {

        $result = $this->vehicleService->delete($vehicle);
        if ($result) {
            return $this->success([], 'Vehicle successfully removed!', 200);
        }
        return $this->success([], 'Something Went Wrong!', 500);
    }


    public function deleteImage(int $imageId): JsonResponse
    {
        $this->vehicleService->removeImage($imageId);
        return response()->json(['status' => true, 'message' => 'Gallery image deleted successfully!']);
    }


    public function getByGarage(int $garageId): JsonResponse
    {
        $vehicles = $this->vehicleService->byGarage($garageId);
        return response()->json(['status' => true, 'data' => $vehicles]);
    }
}
