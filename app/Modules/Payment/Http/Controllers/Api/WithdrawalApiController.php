<?php

namespace App\Modules\Payment\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Payment\Enums\WithdrawalStatus;
use App\Modules\Payment\Http\Requests\WithdrawalRequestForm;
use App\Modules\Payment\Http\Resources\WithdrawalResource;
use App\Modules\Payment\Models\WithdrawalRequest;
use App\Modules\Payment\Services\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawalApiController extends Controller
{
    public function __construct(
        protected WalletService $walletService
    ) {
        parent::__construct();
    }

    /**
     * User's withdrawal requests
     */
    public function index(Request $request): JsonResponse
    {
        $userId = auth('api')->id() ?? auth()->id();
        $withdrawals = WithdrawalRequest::where('user_id', $userId)
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->input('status')))
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return $this->paginated($withdrawals, WithdrawalResource::class, 'Withdrawal requests retrieved successfully');
    }

    /**
     * Request a withdrawal
     */
    public function store(WithdrawalRequestForm $request): JsonResponse
    {
        $user = auth('api')->user() ?? auth()->user();
        if (!$user) {
            return $this->error('Unauthenticated', 401);
        }

        $wallet = $this->walletService->getOrCreateWallet($user);
        if (!$wallet->canTransact()) {
            return $this->error('Wallet is frozen or inactive', 403);
        }

        $amount = (float) $request->input('amount');
        $fee = 0.00; // Can be configured per method
        $payableAmount = $amount - $fee;

        if (!$wallet->hasSufficientBalance($amount)) {
            return $this->error('Insufficient wallet balance for this withdrawal amount', 422);
        }

        try {
            $withdrawal = DB::transaction(function () use ($user, $wallet, $amount, $fee, $payableAmount, $request) {
                // Deduct balance from wallet immediately as pending withdrawal
                $trx = $this->walletService->withdraw(
                    user: $user,
                    amount: $amount,
                    description: 'Withdrawal request to ' . $request->input('method')
                );

                return WithdrawalRequest::create([
                    'uuid' => (string) \Illuminate\Support\Str::uuid(),
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'fee' => $fee,
                    'net_amount' => $payableAmount,
                    'currency' => $wallet->currency,
                    'method' => $request->input('method'),
                    'account_details' => $request->input('account_details'),
                    'status' => WithdrawalStatus::PENDING->value,
                ]);
            });

            return $this->success(new WithdrawalResource($withdrawal), 'Withdrawal request submitted successfully', 201);
        } catch (\Throwable $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Cancel a pending withdrawal request
     */
    public function cancel(int|string $id): JsonResponse
    {
        $userId = auth('api')->id() ?? auth()->id();
        $withdrawal = WithdrawalRequest::where('user_id', $userId)->where('id', $id)->firstOrFail();

        $statusVal = $withdrawal->status instanceof WithdrawalStatus ? $withdrawal->status->value : (string) $withdrawal->status;
        if ($statusVal !== WithdrawalStatus::PENDING->value) {
            return $this->error('Only pending withdrawal requests can be cancelled', 422);
        }

        try {
            DB::transaction(function () use ($withdrawal) {
                $withdrawal->status = WithdrawalStatus::CANCELLED->value;
                $withdrawal->rejection_reason = 'Cancelled by user';
                $withdrawal->save();

                // Refund the amount back to user wallet
                $this->walletService->refund(
                    user: $withdrawal->user,
                    amount: (float) $withdrawal->amount,
                    reference: $withdrawal,
                    description: 'Refund for cancelled withdrawal #' . $withdrawal->id
                );
            });

            return $this->success(new WithdrawalResource($withdrawal->fresh()), 'Withdrawal request cancelled and funds refunded');
        } catch (\Throwable $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}
