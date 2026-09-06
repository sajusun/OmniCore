<?php

declare(strict_types=1);

namespace App\Modules\Affiliate\Services;

use App\Models\User;
use App\Modules\Affiliate\Enums\AffiliateStatus;
use App\Modules\Affiliate\Enums\CommissionType;
use App\Modules\Affiliate\Enums\ReferralStatus;
use App\Modules\Affiliate\Models\AffiliateAccount;
use App\Modules\Affiliate\Models\AffiliateCommission;
use App\Modules\Affiliate\Models\AffiliateReferral;
use App\Modules\Notification\Services\NotificationService;
use App\Modules\Payment\Services\WalletService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class AffiliateService
{
    public function __construct(
        protected NotificationService $notificationService,
        protected WalletService $walletService
    ) {}

    /**
     * Get or create affiliate account for user.
     */
    public function getOrCreateAccount(User $user): AffiliateAccount
    {
        return AffiliateAccount::firstOrCreate(
            ['user_id' => $user->id],
            [
                'referral_code'   => 'REF-' . strtoupper(Str::random(6)),
                'commission_rate' => 10.00,
                'commission_type' => CommissionType::PERCENTAGE->value,
                'status'          => AffiliateStatus::ACTIVE->value,
            ]
        );
    }

    /**
     * Track an inbound referral click.
     */
    public function trackVisit(
        string $referralCode,
        ?string $ip = null,
        ?string $userAgent = null,
        ?string $landingPage = null,
        ?string $campaign = null
    ): ?AffiliateReferral {
        $account = AffiliateAccount::where('referral_code', $referralCode)
            ->orWhere('custom_slug', $referralCode)
            ->first();

        if (!$account || $account->status !== AffiliateStatus::ACTIVE) {
            return null;
        }

        return DB::transaction(function () use ($account, $ip, $userAgent, $landingPage, $campaign) {
            $account->increment('lifetime_referrals');

            return AffiliateReferral::create([
                'affiliate_id' => $account->id,
                'visitor_ip'   => $ip,
                'user_agent'   => $userAgent ? Str::limit($userAgent, 255) : null,
                'landing_page' => $landingPage ? Str::limit($landingPage, 255) : null,
                'campaign'     => $campaign,
                'status'       => ReferralStatus::PENDING->value,
            ]);
        });
    }

    /**
     * Link newly registered user to an affiliate.
     */
    public function linkUserToAffiliate(User $user, string $referralCode): ?AffiliateReferral
    {
        $account = AffiliateAccount::where('referral_code', $referralCode)
            ->orWhere('custom_slug', $referralCode)
            ->first();

        if (!$account || $account->user_id === $user->id || $account->status !== AffiliateStatus::ACTIVE) {
            return null;
        }

        $referral = AffiliateReferral::where('affiliate_id', $account->id)
            ->whereNull('referred_user_id')
            ->latest()
            ->first();

        if ($referral) {
            $referral->update(['referred_user_id' => $user->id]);
            return $referral->fresh();
        }

        $account->increment('lifetime_referrals');

        return AffiliateReferral::create([
            'affiliate_id'     => $account->id,
            'referred_user_id' => $user->id,
            'status'           => ReferralStatus::PENDING->value,
        ]);
    }

    /**
     * Record conversion and credit affiliate commission.
     */
    public function recordConversion(
        User $referredUser,
        float $orderAmount,
        ?Model $reference = null
    ): ?AffiliateCommission {
        $referral = AffiliateReferral::where('referred_user_id', $referredUser->id)->first();
        if (!$referral) {
            return null;
        }

        $account = $referral->affiliate;
        if (!$account || $account->status !== AffiliateStatus::ACTIVE) {
            return null;
        }

        return DB::transaction(function () use ($account, $referral, $referredUser, $orderAmount, $reference) {
            // Calculate commission amount
            if ($account->commission_type === CommissionType::PERCENTAGE) {
                $commissionAmount = round(($orderAmount * (float) $account->commission_rate) / 100, 2);
            } else {
                $commissionAmount = (float) $account->commission_rate;
            }

            $commission = AffiliateCommission::create([
                'affiliate_id'      => $account->id,
                'referral_id'       => $referral->id,
                'reference_type'    => $reference ? get_class($reference) : null,
                'reference_id'      => $reference?->id,
                'order_amount'      => $orderAmount,
                'commission_rate'   => $account->commission_rate,
                'commission_amount' => $commissionAmount,
                'status'            => 'approved',
                'approved_at'       => now(),
            ]);

            // Update referral status
            $referral->update([
                'status'       => ReferralStatus::CONVERTED->value,
                'converted_at' => now(),
            ]);

            // Update account balance
            $account->increment('lifetime_conversions');
            $account->update([
                'total_earnings'  => (float) $account->total_earnings + $commissionAmount,
                'current_balance' => (float) $account->current_balance + $commissionAmount,
            ]);

            // Send notification to affiliate user
            $this->notificationService->send(
                $account->user,
                title: "Affiliate Commission Earned! ($" . number_format($commissionAmount, 2) . ")",
                body: "A referred user completed an order of $" . number_format($orderAmount, 2) . ". Your commission has been credited.",
                type: 'affiliate_commission',
                referenceType: 'commission',
                referenceId: $commission->id,
                meta: ['commission_id' => $commission->id, 'amount' => $commissionAmount]
            );

            return $commission;
        });
    }

    /**
     * Cash out affiliate earnings directly to the in-app Wallet.
     */
    public function payoutToWallet(AffiliateAccount $account, float $amount): bool
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Payout amount must be greater than zero.');
        }

        if ((float) $account->current_balance < $amount) {
            throw new InvalidArgumentException('Insufficient affiliate balance for payout.');
        }

        return DB::transaction(function () use ($account, $amount) {
            // Deduct from affiliate balance
            $account->update([
                'current_balance' => (float) $account->current_balance - $amount,
                'paid_earnings'   => (float) $account->paid_earnings + $amount,
            ]);

            // Deposit into user's wallet
            $this->walletService->deposit(
                $account->user,
                $amount,
                $account,
                "Affiliate commission payout"
            );

            // Mark oldest approved commissions as paid
            AffiliateCommission::where('affiliate_id', $account->id)
                ->where('status', 'approved')
                ->update([
                    'status'  => 'paid',
                    'paid_at' => now(),
                ]);

            // Notify user
            $this->notificationService->send(
                $account->user,
                title: "Affiliate Payout Processed",
                body: "$" . number_format($amount, 2) . " has been deposited directly into your Wallet balance.",
                type: 'affiliate_payout',
                referenceType: 'affiliate',
                referenceId: $account->id,
                meta: ['payout_amount' => $amount]
            );

            return true;
        });
    }

    /**
     * Update custom slug.
     */
    public function updateCustomSlug(AffiliateAccount $account, string $slug): AffiliateAccount
    {
        $slug = Str::slug($slug);
        $account->update(['custom_slug' => $slug]);
        return $account->fresh();
    }
}
