<?php

declare(strict_types=1);

namespace App\Modules\Affiliate\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Affiliate\Resources\AffiliateAccountResource;
use App\Modules\Affiliate\Resources\AffiliateCommissionResource;
use App\Modules\Affiliate\Resources\AffiliateReferralResource;
use App\Modules\Affiliate\Services\AffiliateService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class AffiliateApiController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected AffiliateService $affiliateService
    ) {}

    /**
     * Get or create current user's affiliate account.
     */
    public function account(Request $request): JsonResponse
    {
        $account = $this->affiliateService->getOrCreateAccount($request->user());

        return $this->success(
            new AffiliateAccountResource($account),
            'Affiliate account retrieved successfully.'
        );
    }

    /**
     * Track an inbound referral visit (Public).
     */
    public function track(Request $request): JsonResponse
    {
        $request->validate([
            'ref'          => 'required|string',
            'landing_page' => 'nullable|string',
            'campaign'     => 'nullable|string',
        ]);

        $referral = $this->affiliateService->trackVisit(
            $request->ref,
            $request->ip(),
            $request->userAgent(),
            $request->landing_page,
            $request->campaign
        );

        if (!$referral) {
            return $this->error('Invalid or inactive referral code.', 404);
        }

        return $this->success([
            'tracked' => true,
            'code'    => $request->ref,
        ], 'Referral visit tracked successfully.');
    }

    /**
     * List affiliate's referral visits and signups.
     */
    public function referrals(Request $request): JsonResponse
    {
        $account = $this->affiliateService->getOrCreateAccount($request->user());
        $referrals = $account->referrals()->with('referredUser')->orderBy('created_at', 'desc')->paginate($request->integer('per_page', 15));

        return $this->paginated(
            $referrals,
            AffiliateReferralResource::class,
            'Referrals retrieved successfully.'
        );
    }

    /**
     * List affiliate's commissions.
     */
    public function commissions(Request $request): JsonResponse
    {
        $account = $this->affiliateService->getOrCreateAccount($request->user());
        $commissions = $account->commissions()->orderBy('created_at', 'desc')->paginate($request->integer('per_page', 15));

        return $this->paginated(
            $commissions,
            AffiliateCommissionResource::class,
            'Commissions retrieved successfully.'
        );
    }

    /**
     * Update custom referral vanity slug.
     */
    public function updateSlug(Request $request): JsonResponse
    {
        $account = $this->affiliateService->getOrCreateAccount($request->user());

        $request->validate([
            'custom_slug' => 'required|string|alpha_dash|max:50|unique:affiliate_accounts,custom_slug,' . $account->id,
        ]);

        $account = $this->affiliateService->updateCustomSlug($account, $request->custom_slug);

        return $this->success(
            new AffiliateAccountResource($account),
            'Custom referral slug updated.'
        );
    }

    /**
     * Request payout from affiliate balance directly to Wallet.
     */
    public function payout(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $account = $this->affiliateService->getOrCreateAccount($request->user());

        try {
            $this->affiliateService->payoutToWallet($account, (float) $request->amount);

            return $this->success(
                new AffiliateAccountResource($account->fresh()),
                'Payout of $' . number_format((float) $request->amount, 2) . ' successfully deposited to your Wallet.'
            );
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 422);
        }
    }
}
