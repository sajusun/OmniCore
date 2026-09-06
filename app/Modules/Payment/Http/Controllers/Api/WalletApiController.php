<?php

namespace App\Modules\Payment\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Payment\Enums\PaymentMethod;
use App\Modules\Payment\Http\Requests\DepositRequest;
use App\Modules\Payment\Http\Requests\TransferRequest;
use App\Modules\Payment\Http\Resources\PaymentResource;
use App\Modules\Payment\Http\Resources\TransactionResource;
use App\Modules\Payment\Http\Resources\WalletResource;
use App\Modules\Payment\Models\WalletTransaction;
use App\Modules\Payment\Services\PaymentService;
use App\Modules\Payment\Services\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletApiController extends Controller
{
    public function __construct(
        protected WalletService $walletService,
        protected PaymentService $paymentService
    ) {
        parent::__construct();
    }

    /**
     * Get user's wallet overview
     */
    public function balance(): JsonResponse
    {
        $user = auth('api')->user() ?? auth()->user();
        if (!$user) {
            return $this->error('Unauthenticated', 401);
        }

        $wallet = $this->walletService->getOrCreateWallet($user);

        return $this->success(new WalletResource($wallet), 'Wallet details retrieved successfully');
    }

    /**
     * Get user's wallet transactions
     */
    public function transactions(Request $request): JsonResponse
    {
        $userId = auth('api')->id() ?? auth()->id();
        $transactions = WalletTransaction::where('user_id', $userId)
            ->when($request->filled('type'), fn($q) => $q->where('type', $request->input('type')))
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return $this->paginated($transactions, TransactionResource::class, 'Wallet transactions retrieved successfully');
    }

    /**
     * Deposit into wallet via payment gateway
     */
    public function deposit(DepositRequest $request): JsonResponse
    {
        $user = auth('api')->user() ?? auth()->user();
        if (!$user) {
            return $this->error('Unauthenticated', 401);
        }

        $wallet = $this->walletService->getOrCreateWallet($user, $request->input('currency', 'USD'));
        if (!$wallet->canTransact()) {
            return $this->error('Wallet is frozen or inactive', 403);
        }

        try {
            $method = PaymentMethod::from($request->input('method'));
            $payment = $this->paymentService->initiatePayment(
                user: $user,
                payable: $wallet,
                amount: (float) $request->input('amount'),
                method: $method,
                options: [
                    'currency' => $wallet->currency,
                    'metadata' => ['type' => 'wallet_deposit'],
                ]
            );

            return $this->success([
                'payment' => new PaymentResource($payment),
                'payment_url' => $payment->payment_url,
                'status' => $payment->status instanceof \App\Modules\Payment\Enums\PaymentStatus ? $payment->status->value : (string) $payment->status,
            ], 'Deposit initiated successfully');
        } catch (\Throwable $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Peer-to-peer transfer to another user
     */
    public function transfer(TransferRequest $request): JsonResponse
    {
        $sender = auth('api')->user() ?? auth()->user();
        if (!$sender) {
            return $this->error('Unauthenticated', 401);
        }

        if ($request->filled('recipient_id')) {
            $recipient = User::findOrFail($request->input('recipient_id'));
        } else {
            $recipient = User::where('email', $request->input('recipient_email'))->firstOrFail();
        }

        if ($sender->id === $recipient->id) {
            return $this->error('You cannot transfer money to yourself', 422);
        }

        try {
            $result = $this->walletService->transfer(
                sender: $sender,
                recipient: $recipient,
                amount: (float) $request->input('amount'),
                note: $request->input('note', '')
            );

            return $this->success([
                'transaction' => new TransactionResource($result['sender_trx']),
                'current_balance' => (float) $result['sender_trx']->balance_after,
            ], 'Transfer sent successfully');
        } catch (\Throwable $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}
