<?php

namespace App\Modules\Payment\Services;

use App\Models\User;
use App\Modules\Payment\Enums\TransactionType;
use App\Modules\Payment\Models\Wallet;
use App\Modules\Payment\Models\WalletTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

class WalletService
{
    public function getOrCreateWallet(User $user, string $currency = 'USD'): Wallet
    {
        return Wallet::firstOrCreate(
            ['user_id' => $user->id],
            [
                'balance' => 0.00,
                'frozen_balance' => 0.00,
                'currency' => strtoupper($currency),
                'status' => 'active',
            ]
        );
    }

    public function generateTrxId(string $prefix = 'TRX'): string
    {
        return strtoupper($prefix . '_' . date('YmdHis') . '_' . Str::random(6));
    }

    public function deposit(
        User $user,
        float $amount,
        ?Model $reference = null,
        string $description = 'Wallet deposit',
        array $metadata = []
    ): WalletTransaction {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Deposit amount must be greater than zero');
        }

        return DB::transaction(function () use ($user, $amount, $reference, $description, $metadata) {
            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();
            if (!$wallet) {
                $wallet = $this->getOrCreateWallet($user);
                $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();
            }

            if (!$wallet->canTransact()) {
                throw new RuntimeException('Wallet is frozen or inactive.');
            }

            $balanceBefore = (float) $wallet->balance;
            $balanceAfter = $balanceBefore + $amount;

            $wallet->balance = $balanceAfter;
            $wallet->save();

            return WalletTransaction::create([
                'uuid' => $this->generateTrxId('DEP'),
                'wallet_id' => $wallet->id,
                'user_id' => $user->id,
                'type' => TransactionType::DEPOSIT->value,
                'amount' => $amount,
                'before_balance' => $balanceBefore,
                'after_balance' => $balanceAfter,
                'fee' => 0.00,
                'currency' => $wallet->currency,
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id' => $reference?->getKey(),
                'description' => $description,
                'metadata' => $metadata,
            ]);
        });
    }

    public function withdraw(
        User $user,
        float $amount,
        ?Model $reference = null,
        string $description = 'Wallet withdrawal',
        float $fee = 0.00,
        array $metadata = []
    ): WalletTransaction {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Withdrawal amount must be greater than zero');
        }

        $totalDeduction = $amount + $fee;

        return DB::transaction(function () use ($user, $amount, $fee, $totalDeduction, $reference, $description, $metadata) {
            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();
            if (!$wallet || !$wallet->canTransact()) {
                throw new RuntimeException('Wallet is unavailable or frozen.');
            }

            $balanceBefore = (float) $wallet->balance;
            if ($balanceBefore < $totalDeduction) {
                throw new RuntimeException('Insufficient wallet balance for withdrawal.');
            }

            $balanceAfter = $balanceBefore - $totalDeduction;
            $wallet->balance = $balanceAfter;
            $wallet->save();

            return WalletTransaction::create([
                'uuid' => $this->generateTrxId('WTH'),
                'wallet_id' => $wallet->id,
                'user_id' => $user->id,
                'type' => TransactionType::WITHDRAWAL->value,
                'amount' => $amount,
                'before_balance' => $balanceBefore,
                'after_balance' => $balanceAfter,
                'fee' => $fee,
                'currency' => $wallet->currency,
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id' => $reference?->getKey(),
                'description' => $description,
                'metadata' => $metadata,
            ]);
        });
    }

    public function transfer(User $sender, User $recipient, float $amount, string $note = ''): array
    {
        if ($sender->id === $recipient->id) {
            throw new InvalidArgumentException('Cannot transfer funds to your own wallet');
        }
        if ($amount <= 0) {
            throw new InvalidArgumentException('Transfer amount must be greater than zero');
        }

        return DB::transaction(function () use ($sender, $recipient, $amount, $note) {
            $firstId = min($sender->id, $recipient->id);
            $secondId = max($sender->id, $recipient->id);

            Wallet::where('user_id', $firstId)->lockForUpdate()->first();
            Wallet::where('user_id', $secondId)->lockForUpdate()->first();

            $senderWallet = $this->getOrCreateWallet($sender);
            $recipientWallet = $this->getOrCreateWallet($recipient);

            if (!$senderWallet->canTransact() || !$recipientWallet->canTransact()) {
                throw new RuntimeException('One of the participant wallets is inactive or frozen.');
            }

            $senderBalBefore = (float) $senderWallet->balance;
            if ($senderBalBefore < $amount) {
                throw new RuntimeException('Insufficient balance to transfer funds.');
            }

            $senderBalAfter = $senderBalBefore - $amount;
            $senderWallet->balance = $senderBalAfter;
            $senderWallet->save();

            $recipientBalBefore = (float) $recipientWallet->balance;
            $recipientBalAfter = $recipientBalBefore + $amount;
            $recipientWallet->balance = $recipientBalAfter;
            $recipientWallet->save();

            $trxId = $this->generateTrxId('TRF');

            $senderTrx = WalletTransaction::create([
                'uuid' => $trxId . '_OUT',
                'wallet_id' => $senderWallet->id,
                'user_id' => $sender->id,
                'type' => TransactionType::TRANSFER_OUT->value,
                'amount' => $amount,
                'before_balance' => $senderBalBefore,
                'after_balance' => $senderBalAfter,
                'fee' => 0.00,
                'currency' => $senderWallet->currency,
                'reference_type' => User::class,
                'reference_id' => $recipient->id,
                'description' => 'Transferred to ' . ($recipient->name ?: $recipient->email) . ($note ? ": {$note}" : ''),
                'metadata' => ['recipient_id' => $recipient->id, 'recipient_name' => $recipient->name],
            ]);

            $recipientTrx = WalletTransaction::create([
                'uuid' => $trxId . '_IN',
                'wallet_id' => $recipientWallet->id,
                'user_id' => $recipient->id,
                'type' => TransactionType::TRANSFER_IN->value,
                'amount' => $amount,
                'before_balance' => $recipientBalBefore,
                'after_balance' => $recipientBalAfter,
                'fee' => 0.00,
                'currency' => $recipientWallet->currency,
                'reference_type' => User::class,
                'reference_id' => $sender->id,
                'description' => 'Received from ' . ($sender->name ?: $sender->email) . ($note ? ": {$note}" : ''),
                'metadata' => ['sender_id' => $sender->id, 'sender_name' => $sender->name],
            ]);

            return [
                'sender_trx' => $senderTrx,
                'recipient_trx' => $recipientTrx,
            ];
        });
    }

    public function pay(
        User $user,
        float $amount,
        ?Model $reference = null,
        string $description = 'Payment from wallet'
    ): WalletTransaction {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Payment amount must be greater than zero');
        }

        return DB::transaction(function () use ($user, $amount, $reference, $description) {
            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();
            if (!$wallet || !$wallet->canTransact()) {
                throw new RuntimeException('Wallet is unavailable or frozen.');
            }

            $balanceBefore = (float) $wallet->balance;
            if ($balanceBefore < $amount) {
                throw new RuntimeException('Insufficient wallet balance for payment.');
            }

            $balanceAfter = $balanceBefore - $amount;
            $wallet->balance = $balanceAfter;
            $wallet->save();

            return WalletTransaction::create([
                'uuid' => $this->generateTrxId('PAY'),
                'wallet_id' => $wallet->id,
                'user_id' => $user->id,
                'type' => TransactionType::PAYMENT->value,
                'amount' => $amount,
                'before_balance' => $balanceBefore,
                'after_balance' => $balanceAfter,
                'fee' => 0.00,
                'currency' => $wallet->currency,
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id' => $reference?->getKey(),
                'description' => $description,
            ]);
        });
    }

    public function refund(
        User $user,
        float $amount,
        ?Model $reference = null,
        string $description = 'Payment refund to wallet'
    ): WalletTransaction {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Refund amount must be greater than zero');
        }

        return DB::transaction(function () use ($user, $amount, $reference, $description) {
            $wallet = $this->getOrCreateWallet($user);
            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();

            $balanceBefore = (float) $wallet->balance;
            $balanceAfter = $balanceBefore + $amount;
            $wallet->balance = $balanceAfter;
            $wallet->save();

            return WalletTransaction::create([
                'uuid' => $this->generateTrxId('REF'),
                'wallet_id' => $wallet->id,
                'user_id' => $user->id,
                'type' => TransactionType::REFUND->value,
                'amount' => $amount,
                'before_balance' => $balanceBefore,
                'after_balance' => $balanceAfter,
                'fee' => 0.00,
                'currency' => $wallet->currency,
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id' => $reference?->getKey(),
                'description' => $description,
            ]);
        });
    }

    public function adjustBalance(
        User $user,
        float $amount,
        string $type = 'credit',
        string $reason = 'Admin balance adjustment',
        ?User $admin = null
    ): WalletTransaction {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Adjustment amount must be greater than zero');
        }

        return DB::transaction(function () use ($user, $amount, $type, $reason, $admin) {
            $wallet = $this->getOrCreateWallet($user);
            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();

            $balanceBefore = (float) $wallet->balance;

            if ($type === 'debit') {
                if ($balanceBefore < $amount) {
                    throw new RuntimeException('Cannot debit more than user wallet balance.');
                }
                $balanceAfter = $balanceBefore - $amount;
            } else {
                $balanceAfter = $balanceBefore + $amount;
            }

            $wallet->balance = $balanceAfter;
            $wallet->save();

            return WalletTransaction::create([
                'uuid' => $this->generateTrxId('ADJ'),
                'wallet_id' => $wallet->id,
                'user_id' => $user->id,
                'type' => TransactionType::ADJUSTMENT->value,
                'amount' => $amount,
                'before_balance' => $balanceBefore,
                'after_balance' => $balanceAfter,
                'fee' => 0.00,
                'currency' => $wallet->currency,
                'reference_type' => $admin ? User::class : null,
                'reference_id' => $admin?->id,
                'description' => $reason . ($admin ? " (by Admin #{$admin->id})" : ''),
                'metadata' => [
                    'adjustment_type' => $type,
                    'admin_id' => $admin?->id,
                ],
            ]);
        });
    }
}
