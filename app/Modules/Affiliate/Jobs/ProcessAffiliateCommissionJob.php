<?php

namespace App\Modules\Affiliate\Jobs;

use App\Models\User;
use App\Modules\Affiliate\Services\AffiliateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessAffiliateCommissionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 15;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $referredUser,
        public float $orderAmount,
        public ?Model $reference = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(AffiliateService $affiliateService): void
    {
        try {
            $commission = $affiliateService->recordConversion(
                $this->referredUser,
                $this->orderAmount,
                $this->reference
            );

            if ($commission) {
                Log::info('Affiliate commission calculated and credited asynchronously', [
                    'commission_id' => $commission->id,
                    'affiliate_id' => $commission->affiliate_id,
                    'amount' => $commission->commission_amount,
                ]);
            }
        } catch (Throwable $e) {
            Log::error('ProcessAffiliateCommissionJob failed: ' . $e->getMessage(), [
                'referred_user_id' => $this->referredUser->id,
                'order_amount' => $this->orderAmount,
            ]);
            throw $e;
        }
    }
}
