<?php

namespace App\Modules\Subscription\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Modules\Subscription\Http\Requests\PlanStoreUpdateRequest;
use App\Modules\Subscription\Models\Plan;
use App\Modules\Subscription\Models\PlanFeature;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PlanAdminController extends Controller
{
    public function index(): View
    {
        $plans = Plan::with('features')->orderBy('sort_order', 'asc')->get();
        return view('subscription::backend.plans.index', compact('plans'));
    }

    public function create(): View
    {
        $plan = new Plan();
        return view('subscription::backend.plans.form', compact('plan'));
    }

    public function store(PlanStoreUpdateRequest $request): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request) {
                $plan = Plan::create([
                    'name' => $request->input('name'),
                    'slug' => $request->input('slug'),
                    'description' => $request->input('description'),
                    'price' => (float) $request->input('price'),
                    'signup_fee' => (float) $request->input('signup_fee', 0),
                    'currency' => $request->input('currency', 'USD'),
                    'billing_interval' => $request->input('billing_interval'),
                    'interval_count' => (int) $request->input('interval_count', 1),
                    'trial_days' => (int) $request->input('trial_days', 0),
                    'is_popular' => $request->boolean('is_popular'),
                    'is_active' => $request->boolean('is_active', true),
                    'sort_order' => (int) $request->input('sort_order', 0),
                ]);

                if ($request->filled('features')) {
                    foreach ($request->input('features') as $index => $feat) {
                        if (!empty($feat['code'])) {
                            PlanFeature::create([
                                'plan_id' => $plan->id,
                                'name' => $feat['name'] ?? $feat['code'],
                                'code' => $feat['code'],
                                'value' => $feat['value'] ?? 'true',
                                'is_limited' => !empty($feat['is_limited']),
                                'sort_order' => $index,
                            ]);
                        }
                    }
                }
            });

            return redirect()->route('admin.plans.index')->with('success', 'Plan created successfully.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(Plan $plan): View
    {
        $plan->load('features');
        return view('subscription::backend.plans.form', compact('plan'));
    }

    public function update(PlanStoreUpdateRequest $request, Plan $plan): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request, $plan) {
                $plan->update([
                    'name' => $request->input('name'),
                    'slug' => $request->input('slug'),
                    'description' => $request->input('description'),
                    'price' => (float) $request->input('price'),
                    'signup_fee' => (float) $request->input('signup_fee', 0),
                    'currency' => $request->input('currency', 'USD'),
                    'billing_interval' => $request->input('billing_interval'),
                    'interval_count' => (int) $request->input('interval_count', 1),
                    'trial_days' => (int) $request->input('trial_days', 0),
                    'is_popular' => $request->boolean('is_popular'),
                    'is_active' => $request->boolean('is_active'),
                    'sort_order' => (int) $request->input('sort_order', 0),
                ]);

                if ($request->has('features')) {
                    $plan->features()->delete();
                    foreach ($request->input('features') as $index => $feat) {
                        if (!empty($feat['code'])) {
                            PlanFeature::create([
                                'plan_id' => $plan->id,
                                'name' => $feat['name'] ?? $feat['code'],
                                'code' => $feat['code'],
                                'value' => $feat['value'] ?? 'true',
                                'is_limited' => !empty($feat['is_limited']),
                                'sort_order' => $index,
                            ]);
                        }
                    }
                }
            });

            return redirect()->route('admin.plans.index')->with('success', 'Plan updated successfully.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Plan $plan): RedirectResponse
    {
        if ($plan->subscriptions()->where('status', 'active')->exists()) {
            return back()->with('error', 'Cannot delete a plan with active subscribers. Please deactivate it instead.');
        }

        $plan->delete();
        return back()->with('success', 'Plan deleted successfully.');
    }
}
