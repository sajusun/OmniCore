<?php

namespace App\Modules\Order\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Order\Http\Resources\ShippingMethodResource;
use App\Modules\Order\Models\ShippingMethod;
use Illuminate\Http\JsonResponse;

class ShippingMethodController extends Controller
{
    public function index(): JsonResponse
    {
        $methods = ShippingMethod::where('is_active', true)->orderBy('cost', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => ShippingMethodResource::collection($methods),
        ]);
    }
}
