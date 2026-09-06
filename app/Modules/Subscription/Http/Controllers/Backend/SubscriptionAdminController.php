<?php

namespace App\Modules\Subscription\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Subscription\Enums\SubscriptionStatus;
use App\Modules\Subscription\Models\Plan;
use App\Modules\Subscription\Models\Subscription;
use App\Modules\Subscription\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionAdminController extends Controller
{
    public function __construct(
        protected SubscriptionService $subscriptionService
    ) {
        parent::__construct();
    }

    public function index(Request $request): View
    {
        $subscriptions = Subscription::with(['user', 'plan'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('plan_id'), fn($q) => $q->where('plan_id', $request->input('plan_id')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->input('search');
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate(20);

        $stats = [
            'total_subscriptions' => Subscription::count(),
            'active_count' => Subscription::where('status', 'active')->count(),
            'trial_count' => Subscription::where('status', 'trialing')->count(),
            'canceled_count' => Subscription::where('status', 'canceled')->count(),
        ];

        $plans = Plan::active()->get();

        return view('subscription::backend.subscriptions.index', compact('subscriptions', 'stats', 'plans'));
    }

    public function show(Subscription $subscription): View
    {
        $subscription->load(['user', 'plan.features', 'usages', 'payments']);
        return view('subscription::backend.subscriptions.show', compact('subscription'));
    }

    public function grant(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:plans,id',
        ]);

        try {
            $user = User::findOrFail($request->input('user_id'));
            $plan = Plan::findOrFail($request->input('plan_id'));

            $this->subscriptionService->subscribe(
                user: $user,
                plan: $plan,
                paymentMethod: 'admin_manual'
            );

            return back()->with('success', "Subscription granted to {$user->name} successfully.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancel(Request $request, Subscription $subscription): RedirectResponse
    {
        try {
            $this->subscriptionService->cancel($subscription, $request->boolean('immediately', true));
            return back()->with('success', 'Subscription cancelled.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
