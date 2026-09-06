<?php

namespace App\Modules\Reward\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Modules\Reward\Http\Requests\BadgeStoreUpdateRequest;
use App\Modules\Reward\Models\Badge;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BadgeAdminController extends Controller
{
    public function index(): View
    {
        $badges = Badge::withCount('users')->latest()->get();
        return view('reward::backend.badges.index', compact('badges'));
    }

    public function create(): View
    {
        $badge = new Badge();
        return view('reward::backend.badges.form', compact('badge'));
    }

    public function store(BadgeStoreUpdateRequest $request): RedirectResponse
    {
        try {
            Badge::create([
                'name' => $request->input('name'),
                'slug' => $request->input('slug'),
                'description' => $request->input('description'),
                'badge_type' => $request->input('badge_type'),
                'points_reward' => (int) $request->input('points_reward', 0),
                'criteria_type' => $request->input('criteria_type'),
                'criteria_threshold' => (int) $request->input('criteria_threshold', 1),
                'is_active' => $request->boolean('is_active', true),
            ]);

            return redirect()->route('admin.badges.index')->with('success', 'Badge created successfully.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(Badge $badge): View
    {
        return view('reward::backend.badges.form', compact('badge'));
    }

    public function update(BadgeStoreUpdateRequest $request, Badge $badge): RedirectResponse
    {
        try {
            $badge->update([
                'name' => $request->input('name'),
                'slug' => $request->input('slug'),
                'description' => $request->input('description'),
                'badge_type' => $request->input('badge_type'),
                'points_reward' => (int) $request->input('points_reward', 0),
                'criteria_type' => $request->input('criteria_type'),
                'criteria_threshold' => (int) $request->input('criteria_threshold', 1),
                'is_active' => $request->boolean('is_active'),
            ]);

            return redirect()->route('admin.badges.index')->with('success', 'Badge updated successfully.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Badge $badge): RedirectResponse
    {
        $badge->delete();
        return back()->with('success', 'Badge deleted successfully.');
    }
}
