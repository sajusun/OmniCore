<?php

namespace App\Modules\Subscription\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Subscription\Http\Resources\PlanResource;
use App\Modules\Subscription\Models\Plan;
use Illuminate\Http\JsonResponse;

class PlanApiController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * List all public active subscription plans
     */
    public function index(): JsonResponse
    {
        $plans = Plan::with('features')->active()->get();
        return $this->success(PlanResource::collection($plans), 'Subscription plans retrieved successfully');
    }

    /**
     * Plan details with feature matrix
     */
    public function show(string $slug): JsonResponse
    {
        $plan = Plan::with('features')
            ->where('slug', $slug)
            ->orWhere('id', $slug)
            ->firstOrFail();

        return $this->success(new PlanResource($plan), 'Plan details retrieved successfully');
    }
}
