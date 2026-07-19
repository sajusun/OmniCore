<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Models\Garage;
use Illuminate\Http\Request; 
use App\Services\GarageService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Garage\StoreGarageRequest;
use App\Http\Requests\Garage\UpdateGarageRequest;

class GarageController extends Controller
{
    protected GarageService $garageService;

    // Service class implementation binding injection layer
    public function __construct(GarageService $garageService)
    {
        $this->garageService = $garageService;
    }

    /**
     * Display a listing of garages.
     */
    public function index(): JsonResponse
    {
        // Custom dynamic logic mapping pipeline layer helper tracking method name update if changed
        $garages = Garage::with('user')->latest()->get(); 
        return response()->json(['status' => 'success', 'data' => $garages]);
    }

    /**
     * Store a newly created garage.
     */
    public function store(StoreGarageRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        // Dynamic payload collection parameters map input matrix structure setup
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo');
        }
        if ($request->hasFile('banner')) {
            $data['banner'] = $request->file('banner');
        }

        $garage = $this->garageService->create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Garage created successfully!',
            'data' => $garage
        ], 201);
    }

    /**
     * Display the specified garage details.
     */
    public function show(int $id): JsonResponse
    {
        $garage = $this->garageService->find($id);
        return response()->json(['status' => 'success', 'data' => $garage]);
    }

    /**
     * Update the specified garage.
     */
    public function update(UpdateGarageRequest $request, Garage $garage): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo');
        }
        if ($request->hasFile('banner')) {
            $data['banner'] = $request->file('banner');
        }

        $updatedGarage = $this->garageService->update($garage, $data);

        return response()->json([
            'status' => 'success',
            'message' => 'Garage updated successfully!',
            'data' => $updatedGarage
        ]);
    }

    /**
     * Remove the specified garage from storage.
     */
    public function destroy(Garage $garage): JsonResponse
    {
        $this->garageService->delete($garage);
        return response()->json(['status' => 'success', 'message' => 'Garage deleted successfully!']);
    }
}