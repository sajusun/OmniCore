<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SubscriptionPlanController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::with('items')
            ->where('is_active', true)
            ->get();

        $plans = $plans->map(function ($plan) {
            $data = $plan->toArray();
            $data['logo'] = $plan->logo ? Helper::getImageUrl($plan->logo) : null;
            $data['items'] = $plan->items->pluck('title')->toArray();
            return $data;
        });

        return Helper::jsonResponse(true, 'Subscription plans retrieved successfully.', 200, $plans);
    }
}
