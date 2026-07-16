<?php

namespace App\Http\Controllers\Web\Backend;

use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPlanItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SubscriptionPlanItemController extends Controller
{
    public function index($planId)
    {
        $plan = SubscriptionPlan::findOrFail($planId);
        $items = SubscriptionPlanItem::where('subscription_plan_id', $plan->id)->orderBy('order')->get();
        return view('backend.layouts.subscription_plans.items', compact('plan', 'items'));
    }

    public function store(Request $request, $planId)
    {
        $plan = SubscriptionPlan::findOrFail($planId);
        
        $request->validate([
            'title' => 'required|string|max:255',
            // 'key' => 'nullable|string|max:255',
            // 'value' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
        ]);

        SubscriptionPlanItem::create([
            'subscription_plan_id' => $plan->id,
            'title' => $request->title,
            // 'key' => $request->key,
            // 'value' => $request->value,
            'order' => $request->order ?? 0,
        ]);

        return redirect()->back()->with('t-success', 'Item added successfully.');
    }

    public function edit($itemId)
    {
        $item = SubscriptionPlanItem::with('plan')->findOrFail($itemId);
        return view('backend.layouts.subscription_plans.item_edit', compact('item'));
    }

    public function update(Request $request, $itemId)
    {
        $item = SubscriptionPlanItem::findOrFail($itemId);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'key' => 'nullable|string|max:255',
            'value' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
        ]);

        $item->update([
            'title' => $request->title,
            'key' => $request->key,
            'value' => $request->value,
            'order' => $request->order ?? 0,
        ]);

        return redirect()->back()->with('t-success', 'Item updated successfully.');
    }

    public function destroy($itemId)
    {
        $item = SubscriptionPlanItem::findOrFail($itemId);
        $item->delete();
        return redirect()->back()->with('t-success', 'Item deleted successfully.');
    }

    public function status($itemId)
    {
        $item = SubscriptionPlanItem::findOrFail($itemId);
        $item->status = !$item->status;
        $item->save();

        return redirect()->back()->with('t-success', 'Status updated successfully.');
    }
}
