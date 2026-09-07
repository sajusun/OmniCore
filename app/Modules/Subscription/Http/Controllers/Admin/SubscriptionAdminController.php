<?php

namespace App\Modules\Subscription\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Subscription\Models\Plan;
use App\Modules\Subscription\Models\PlanFeature;
use App\Modules\Subscription\Models\Subscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SubscriptionAdminController extends Controller
{
    /**
     * List all subscription plans.
     */
    public function plans(): View
    {
        $plans = Plan::with(['features', 'subscriptions'])->orderBy('price')->get();
        $totalSubscribers = Subscription::active()->count();
        $monthlyRevenue = Subscription::active()->whereHas('plan')->with('plan')->get()->sum(fn($s) => $s->plan->price ?? 0);

        return view('subscription_module::admin.plans.index', compact(
            'plans',
            'totalSubscribers',
            'monthlyRevenue'
        ));
    }

    /**
     * Store a new subscription plan.
     */
    public function storePlan(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'duration_days' => 'required|integer|min:1',
            'trial_days' => 'nullable|integer|min:0',
            'is_featured' => 'nullable|boolean',
        ]);

        $plan = Plan::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'currency' => $validated['currency'] ?? 'USD',
            'duration_days' => $validated['duration_days'],
            'trial_days' => $validated['trial_days'] ?? 0,
            'is_active' => true,
            'is_featured' => $request->has('is_featured'),
        ]);

        // Default quotas
        PlanFeature::create([
            'plan_id' => $plan->id,
            'feature_key' => 'max_products',
            'feature_value' => '100',
            'quota_limit' => 100,
        ]);

        return redirect()->back()->with('success', "Plan '{$plan->name}' created successfully.");
    }

    /**
     * Update an existing subscription plan.
     */
    public function updatePlan(Request $request, int $id): RedirectResponse
    {
        $plan = Plan::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
        ]);

        $plan->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'duration_days' => $validated['duration_days'],
            'is_active' => $request->has('is_active'),
            'is_featured' => $request->has('is_featured'),
        ]);

        return redirect()->back()->with('success', "Plan '{$plan->name}' updated successfully.");
    }

    /**
     * Delete a subscription plan.
     */
    public function deletePlan(int $id): RedirectResponse
    {
        $plan = Plan::findOrFail($id);
        $plan->delete();

        return redirect()->back()->with('success', 'Subscription plan deleted.');
    }

    /**
     * List all active user subscriptions.
     */
    public function subscriptions(Request $request): View
    {
        $query = Subscription::with(['user', 'plan'])->latest();

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $subscriptions = $query->paginate(15);

        return view('subscription_module::admin.subscriptions.index', compact('subscriptions'));
    }
}
