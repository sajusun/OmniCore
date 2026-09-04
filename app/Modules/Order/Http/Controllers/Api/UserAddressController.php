<?php

namespace App\Modules\Order\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Order\Http\Resources\UserAddressResource;
use App\Modules\Order\Models\UserAddress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserAddressController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $addresses = $request->user()->addresses()->latest('is_default')->latest()->get();

        return response()->json([
            'success' => true,
            'data' => UserAddressResource::collection($addresses),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'nullable|in:shipping,billing',
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'street_address' => 'required|string|max:255',
            'apartment_suite' => 'nullable|string|max:100',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'nullable|string|max:100',
            'is_default' => 'nullable|boolean',
        ]);

        $user = $request->user();

        if (! empty($validated['is_default'])) {
            UserAddress::where('user_id', $user->id)->update(['is_default' => false]);
        }

        // If this is user's first address, set as default
        if ($user->addresses()->count() === 0) {
            $validated['is_default'] = true;
        }

        $address = $user->addresses()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Address added successfully.',
            'data' => new UserAddressResource($address),
        ], 201);
    }

    public function update(int $id, Request $request): JsonResponse
    {
        $address = UserAddress::where('user_id', $request->user()->id)->findOrFail($id);

        $validated = $request->validate([
            'type' => 'nullable|in:shipping,billing',
            'recipient_name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:30',
            'street_address' => 'sometimes|required|string|max:255',
            'apartment_suite' => 'nullable|string|max:100',
            'city' => 'sometimes|required|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'sometimes|required|string|max:20',
            'country' => 'nullable|string|max:100',
            'is_default' => 'nullable|boolean',
        ]);

        if (! empty($validated['is_default'])) {
            UserAddress::where('user_id', $request->user()->id)->where('id', '!=', $id)->update(['is_default' => false]);
        }

        $address->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Address updated successfully.',
            'data' => new UserAddressResource($address),
        ]);
    }

    public function setDefault(int $id, Request $request): JsonResponse
    {
        $address = UserAddress::where('user_id', $request->user()->id)->findOrFail($id);
        UserAddress::where('user_id', $request->user()->id)->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Default address set.',
            'data' => new UserAddressResource($address),
        ]);
    }

    public function destroy(int $id, Request $request): JsonResponse
    {
        $address = UserAddress::where('user_id', $request->user()->id)->findOrFail($id);
        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Address deleted.',
        ]);
    }
}
