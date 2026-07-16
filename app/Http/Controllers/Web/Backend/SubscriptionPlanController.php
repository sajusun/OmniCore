<?php

namespace App\Http\Controllers\Web\Backend;

use App\Helpers\Helper;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SubscriptionPlanController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::latest()->paginate(10);
        return view('backend.layouts.subscription_plans.index', compact('plans'));
    }

    public function create()
    {
        return view('backend.layouts.subscription_plans.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            // 'billing_cycle' => 'required|in:monthly,yearly,lifetime',
            'title' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'bg' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->except(['logo', 'bg']);
        $data['slug'] = Helper::makeSlug(SubscriptionPlan::class, $request->name);
        $data['billing_cycle']="monthly";
        
        if ($request->hasFile('logo')) {
            $data['logo'] = Helper::uploadImage($request->file('logo'), 'plans');
        }
        
        if ($request->hasFile('bg')) {
            $data['bg'] = Helper::uploadImage($request->file('bg'), 'plans');
        }

        SubscriptionPlan::create($data);

        return redirect()->route('admin.subscription_plans.index')->with('t-success', 'Subscription Plan created successfully.');
    }

    public function edit(SubscriptionPlan $subscriptionPlan)
    {
        return view('backend.layouts.subscription_plans.edit', compact('subscriptionPlan'));
    }

    public function update(Request $request, SubscriptionPlan $subscriptionPlan)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            // 'billing_cycle' => 'required|in:monthly,yearly,lifetime',
            'title' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'bg' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->except(['logo', 'bg']);
        
        if ($request->name !== $subscriptionPlan->name) {
            $data['slug'] = Helper::makeSlug(SubscriptionPlan::class, $request->name);
        }

        if ($request->hasFile('logo')) {
            if ($subscriptionPlan->logo) {
                Helper::fileDelete(public_path($subscriptionPlan->logo));
            }
            $data['logo'] = Helper::uploadImage($request->file('logo'), 'plans');
        }
        
        if ($request->hasFile('bg')) {
            if ($subscriptionPlan->bg) {
                Helper::fileDelete(public_path($subscriptionPlan->bg));
            }
            $data['bg'] = Helper::uploadImage($request->file('bg'), 'plans');
        }

        $subscriptionPlan->update($data);

        return redirect()->route('admin.subscription_plans.index')->with('t-success', 'Subscription Plan updated successfully.');
    }

    public function destroy(SubscriptionPlan $subscriptionPlan)
    {
        if ($subscriptionPlan->logo) {
            Helper::fileDelete(public_path($subscriptionPlan->logo));
        }
        if ($subscriptionPlan->bg) {
            Helper::fileDelete(public_path($subscriptionPlan->bg));
        }
        $subscriptionPlan->delete();
        
        return redirect()->route('admin.subscription_plans.index')->with('t-success', 'Subscription Plan deleted successfully.');
    }

    public function status($id)
    {
        $plan = SubscriptionPlan::findOrFail($id);
        $plan->is_active = !$plan->is_active;
        $plan->save();

        return redirect()->back()->with('t-success', 'Status updated successfully.');
    }
}
