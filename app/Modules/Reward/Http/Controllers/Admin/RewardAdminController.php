<?php

namespace App\Modules\Reward\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Reward\Models\Badge;
use App\Modules\Reward\Models\PointTransaction;
use App\Modules\Reward\Models\RewardTier;
use App\Modules\Reward\Models\UserReward;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RewardAdminController extends Controller
{
    /**
     * Display reward tiers and point settings.
     */
    public function tiers(): View
    {
        $tiers = RewardTier::orderBy('min_points')->get();
        $totalPointsIssued = PointTransaction::where('amount', '>', 0)->sum('amount');
        $totalRedeemedPoints = abs(PointTransaction::where('amount', '<', 0)->sum('amount'));

        return view('reward_module::admin.tiers.index', compact(
            'tiers',
            'totalPointsIssued',
            'totalRedeemedPoints'
        ));
    }

    /**
     * Update a reward tier.
     */
    public function updateTier(Request $request, int $id): RedirectResponse
    {
        $tier = RewardTier::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'min_points' => 'required|integer|min:0',
            'point_multiplier' => 'required|numeric|min:1',
            'cashback_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $tier->update($validated);

        return redirect()->back()->with('success', "Tier '{$tier->name}' updated successfully.");
    }

    /**
     * List and manage gamification badges.
     */
    public function badges(): View
    {
        $badges = Badge::withCount('users')->latest()->get();

        return view('reward_module::admin.badges.index', compact('badges'));
    }

    /**
     * Store new badge.
     */
    public function storeBadge(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'required|string|max:100|unique:badges,slug',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'criteria_type' => 'required|string|in:points,streak,orders,reviews',
            'criteria_value' => 'required|integer|min:1',
        ]);

        Badge::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? 'fa-solid fa-medal',
            'criteria_type' => $validated['criteria_type'],
            'criteria_value' => $validated['criteria_value'],
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Badge created successfully.');
    }

    /**
     * Delete badge.
     */
    public function deleteBadge(int $id): RedirectResponse
    {
        $badge = Badge::findOrFail($id);
        $badge->delete();

        return redirect()->back()->with('success', 'Badge deleted.');
    }

    /**
     * User leaderboard & streaks.
     */
    public function leaderboard(): View
    {
        $topUsers = UserReward::with('user')
            ->orderByDesc('points_balance')
            ->take(25)
            ->get();

        $topStreaks = UserReward::with('user')
            ->orderByDesc('streak_days')
            ->take(25)
            ->get();

        return view('reward_module::admin.leaderboard.index', compact('topUsers', 'topStreaks'));
    }
}
