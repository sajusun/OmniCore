<?php

namespace App\Modules\Subscription\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Subscription\Http\Requests\SubscribeRequest;
use App\Modules\Subscription\Http\Resources\SubscriptionResource;
use App\Modules\Subscription\Models\Plan;
use App\Modules\Subscription\Models\Subscription;
use App\Modules\Subscription\Services\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionApiController extends Controller
{
    public function __construct(
        protected SubscriptionService $subscriptionService
    ) {
        parent::__construct();
    }

    /**
     * Get user's current subscription & usages
     */
    public function current(): JsonResponse
    {
        $user = auth('api')->user() ?? auth()->user();
        if (!$user) {
            return $this->error('Unauthenticated', 401);
        }

        $subscription = $user->currentSubscription();
        if (!$subscription) {
            return $this->success(null, 'No active subscription found');
        }

        return $this->success(new SubscriptionResource($subscription), 'Current subscription retrieved successfully');
    }

    /**
     * Subscribe to a plan
     */
    public function subscribe(SubscribeRequest $request): JsonResponse
    {
        $user = auth('api')->user() ?? auth()->user();
        if (!$user) {
            return $this->error('Unauthenticated', 401);
        }

        $plan = Plan::with('features')->findOrFail($request->input('plan_id'));

        try {
            $subscription = $this->subscriptionService->subscribe(
                user: $user,
                plan: $plan,
                paymentMethod: $request->input('payment_method', 'wallet'),
                autoRenew: $request->boolean('auto_renew', true)
            );

            return $this->success(
                new SubscriptionResource($subscription->load(['plan.features', 'usages'])),
                'Subscribed successfully'
            );
        } catch (\Throwable $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Cancel active subscription
     */
    public function cancel(Request $request): JsonResponse
    {
        $user = auth('api')->user() ?? auth()->user();
        if (!$user) {
            return $this->error('Unauthenticated', 401);
        }

        $subscription = $user->currentSubscription();
        if (!$subscription) {
            return $this->error('No active subscription to cancel', 422);
        }

        try {
            $canceled = $this->subscriptionService->cancel(
                $subscription,
                $request->boolean('immediately', false)
            );

            return $this->success(
                new SubscriptionResource($canceled->load(['plan.features', 'usages'])),
                'Subscription cancelled successfully'
            );
        } catch (\Throwable $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Resume a canceled subscription on grace period
     */
    public function resume(): JsonResponse
    {
        $user = auth('api')->user() ?? auth()->user();
        if (!$user) {
            return $this->error('Unauthenticated', 401);
        }

        $subscription = Subscription::where('user_id', $user->id)
            ->whereNotNull('canceled_at')
            ->where('ends_at', '>', now())
            ->latest('id')
            ->first();

        if (!$subscription) {
            return $this->error('No subscription on grace period available to resume', 422);
        }

        try {
            $resumed = $this->subscriptionService->resume($subscription);
            return $this->success(
                new SubscriptionResource($resumed->load(['plan.features', 'usages'])),
                'Subscription resumed successfully'
            );
        } catch (\Throwable $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Check access or remaining usage for a specific feature code
     */
    public function checkFeature(string $featureCode): JsonResponse
    {
        $user = auth('api')->user() ?? auth()->user();
        if (!$user) {
            return $this->error('Unauthenticated', 401);
        }

        $hasAccess = $this->subscriptionService->canAccessFeature($user, $featureCode);
        $remaining = $this->subscriptionService->getRemainingUsage($user, $featureCode);

        return $this->success([
            'feature_code' => $featureCode,
            'can_access' => $hasAccess,
            'remaining_quota' => $remaining,
        ], 'Feature access queried');
    }
}
