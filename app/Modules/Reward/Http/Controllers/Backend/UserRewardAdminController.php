<?php

namespace App\Modules\Reward\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Reward\Models\RewardTier;
use App\Modules\Reward\Models\UserReward;
use App\Modules\Reward\Services\RewardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserRewardAdminController extends Controller
{
    public function __construct(
        protected RewardService $rewardService
    ) {
        parent::__construct();
    }

    public function index(Request $request): View
    {
        $userRewards = UserReward::with(['user', 'tier'])
            ->when($request->filled('tier_id'), fn($q) => $q->where('tier_id', $request->input('tier_id')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->input('search');
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            })
            ->orderBy('lifetime_points', 'desc')
            ->paginate(20);

        $stats = [
            'total_users_enrolled' => UserReward::count(),
            'total_points_in_circulation' => (int) UserReward::sum('points_balance'),
            'total_points_distributed' => (int) UserReward::sum('lifetime_points'),
        ];

        $tiers = RewardTier::ordered()->get();

        return view('reward::backend.users.index', compact('userRewards', 'stats', 'tiers'));
    }

    public function adjust(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'type' => 'required|in:credit,debit',
            'amount' => 'required|integer|min:1',
            'description' => 'required|string|max:255',
        ]);

        try {
            $amount = $request->integer('amount');
            if ($request->input('type') === 'credit') {
                $this->rewardService->addPoints(
                    user: $user,
                    amount: $amount,
                    description: $request->input('description') . ' (Admin Credit)'
                );
            } else {
                $this->rewardService->spendPoints(
                    user: $user,
                    amount: $amount,
                    description: $request->input('description') . ' (Admin Debit)'
                );
            }

            return back()->with('success', 'User points adjusted successfully.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
