<?php

namespace App\Modules\Reward\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Reward\Http\Requests\RedeemPointsRequest;
use App\Modules\Reward\Http\Resources\BadgeResource;
use App\Modules\Reward\Http\Resources\PointTransactionResource;
use App\Modules\Reward\Http\Resources\RewardOverviewResource;
use App\Modules\Reward\Http\Resources\TierResource;
use App\Modules\Reward\Models\Badge;
use App\Modules\Reward\Models\PointTransaction;
use App\Modules\Reward\Models\RewardTier;
use App\Modules\Reward\Models\UserReward;
use App\Modules\Reward\Services\RewardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RewardApiController extends Controller
{
    public function __construct(
        protected RewardService $rewardService
    ) {
        parent::__construct();
    }

    /**
     * User's reward profile overview
     */
    public function index(): JsonResponse
    {
        $user = auth('api')->user() ?? auth()->user();
        if (!$user) {
            return $this->error('Unauthenticated', 401);
        }

        $reward = $this->rewardService->getOrCreateUserReward($user)->load('tier');
        return $this->success(new RewardOverviewResource($reward), 'Reward overview retrieved');
    }

    /**
     * Claim daily check-in bonus
     */
    public function checkin(): JsonResponse
    {
        $user = auth('api')->user() ?? auth()->user();
        if (!$user) {
            return $this->error('Unauthenticated', 401);
        }

        try {
            $result = $this->rewardService->claimDailyCheckin($user);
            return $this->success($result, "Daily check-in successful! +{$result['points_earned']} points earned.");
        } catch (\Throwable $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Convert points to in-app wallet balance
     */
    public function redeem(RedeemPointsRequest $request): JsonResponse
    {
        $user = auth('api')->user() ?? auth()->user();
        if (!$user) {
            return $this->error('Unauthenticated', 401);
        }

        try {
            $result = $this->rewardService->redeemPointsToWallet(
                $user,
                $request->integer('points')
            );

            return $this->success($result, "Redeemed {$result['points_deducted']} points for \${$result['cash_credited']} Wallet balance!");
        } catch (\Throwable $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Point transaction history
     */
    public function history(Request $request): JsonResponse
    {
        $userId = auth('api')->id() ?? auth()->id();
        $transactions = PointTransaction::where('user_id', $userId)
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return $this->paginated($transactions, PointTransactionResource::class, 'Point transaction history');
    }

    /**
     * List all badges with user unlock status
     */
    public function badges(): JsonResponse
    {
        $user = auth('api')->user() ?? auth()->user();
        $badges = Badge::active()->with(['users' => fn($q) => $q->where('user_id', $user?->id)])->get();

        return $this->success(BadgeResource::collection($badges), 'Badges retrieved');
    }

    /**
     * List all reward tiers & perks
     */
    public function tiers(): JsonResponse
    {
        $tiers = RewardTier::ordered()->get();
        return $this->success(TierResource::collection($tiers), 'Reward tiers retrieved');
    }

    /**
     * Points Leaderboard
     */
    public function leaderboard(Request $request): JsonResponse
    {
        $topUsers = UserReward::with(['user:id,name,username,avatar', 'tier'])
            ->orderBy('lifetime_points', 'desc')
            ->limit($request->integer('limit', 20))
            ->get();

        return $this->success($topUsers, 'Leaderboard retrieved');
    }
}
