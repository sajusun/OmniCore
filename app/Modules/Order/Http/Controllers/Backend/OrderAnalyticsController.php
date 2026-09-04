<?php

namespace App\Modules\Order\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Modules\Order\Services\OrderService;

class OrderAnalyticsController extends Controller
{
    public function __construct(protected OrderService $orderService)
    {
    }

    public function index()
    {
        $analytics = $this->orderService->getEcommerceAnalytics();

        return view('order::backend.analytics.index', compact('analytics'));
    }
}
