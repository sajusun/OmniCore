<?php

namespace App\Modules\Order\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Order\Http\Resources\ShippingMethodResource;
use App\Modules\Order\Models\ShippingMethod;
use Illuminate\Http\JsonResponse;

class ShippingMethodController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(): JsonResponse
    {
        $methods = ShippingMethod::where('is_active', true)->orderBy('cost', 'asc')->get();

        return $this->success(
            ShippingMethodResource::collection($methods),
            'Shipping methods fetched successfully.'
        );
    }
}
