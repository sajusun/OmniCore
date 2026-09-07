<?php

namespace App\Modules\Order\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Order\Services\OrderService;
use Illuminate\Http\JsonResponse;

class AdminOrderAnalyticsController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    /**
     * Get executive e-commerce sales, revenue and metrics dashboard
     */
    public function index(): JsonResponse
    {
        $analytics = $this->orderService->getEcommerceAnalytics();

        return response()->json([
            'success' => true,
            'data' => $analytics,
        ]);
    }
}
