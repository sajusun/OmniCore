<?php

namespace App\Modules\Reward\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Modules\Reward\Http\Requests\TierStoreUpdateRequest;
use App\Modules\Reward\Models\RewardTier;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TierAdminController extends Controller
{
    public function index(): View
    {
        $tiers = RewardTier::ordered()->get();
        return view('reward::backend.tiers.index', compact('tiers'));
    }

    public function create(): View
    {
        $tier = new RewardTier();
        return view('reward::backend.tiers.form', compact('tier'));
    }

    public function store(TierStoreUpdateRequest $request): RedirectResponse
    {
        try {
            RewardTier::create([
                'name' => $request->input('name'),
                'slug' => $request->input('slug'),
                'description' => $request->input('description'),
                'min_points' => (int) $request->input('min_points', 0),
                'point_multiplier' => (float) $request->input('point_multiplier', 1.0),
                'discount_percent' => (float) $request->input('discount_percent', 0),
                'perks' => $request->input('perks', []),
                'color' => $request->input('color', '#8fbd56'),
                'sort_order' => (int) $request->input('sort_order', 0),
            ]);

            return redirect()->route('admin.tiers.index')->with('success', 'Tier created successfully.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(RewardTier $tier): View
    {
        return view('reward::backend.tiers.form', compact('tier'));
    }

    public function update(TierStoreUpdateRequest $request, RewardTier $tier): RedirectResponse
    {
        try {
            $tier->update([
                'name' => $request->input('name'),
                'slug' => $request->input('slug'),
                'description' => $request->input('description'),
                'min_points' => (int) $request->input('min_points', 0),
                'point_multiplier' => (float) $request->input('point_multiplier', 1.0),
                'discount_percent' => (float) $request->input('discount_percent', 0),
                'perks' => $request->input('perks', []),
                'color' => $request->input('color', '#8fbd56'),
                'sort_order' => (int) $request->input('sort_order', 0),
            ]);

            return redirect()->route('admin.tiers.index')->with('success', 'Tier updated successfully.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(RewardTier $tier): RedirectResponse
    {
        $tier->delete();
        return back()->with('success', 'Tier deleted successfully.');
    }
}
