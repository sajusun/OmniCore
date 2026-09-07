<?php

namespace App\Modules\Payment\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Modules\Payment\Enums\WithdrawalStatus;
use App\Modules\Payment\Models\WithdrawalRequest;
use App\Modules\Payment\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WithdrawalAdminController extends Controller
{
    public function __construct(
        protected WalletService $walletService
    ) {
        parent::__construct();
    }

    public function index(Request $request): View
    {
        $withdrawals = WithdrawalRequest::with(['user', 'wallet', 'processor'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->input('search');
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate(20);

        $stats = [
            'pending_count' => WithdrawalRequest::pending()->count(),
            'pending_amount' => (float) WithdrawalRequest::pending()->sum('amount'),
            'approved_count' => WithdrawalRequest::approved()->count(),
            'approved_amount' => (float) WithdrawalRequest::approved()->sum('net_amount'),
        ];

        return view('payment::backend.withdrawals.index', compact('withdrawals', 'stats'));
    }

    public function approve(Request $request, WithdrawalRequest $withdrawal): RedirectResponse
    {
        if ($withdrawal->status !== WithdrawalStatus::PENDING) {
            return back()->with('error', 'Only pending requests can be approved.');
        }

        $request->validate(['admin_note' => 'nullable|string|max:255']);

        $withdrawal->update([
            'status' => WithdrawalStatus::APPROVED,
            'admin_note' => $request->input('admin_note'),
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        return back()->with('success', 'Withdrawal request marked as approved/disbursed.');
    }

    public function reject(Request $request, WithdrawalRequest $withdrawal): RedirectResponse
    {
        if ($withdrawal->status !== WithdrawalStatus::PENDING) {
            return back()->with('error', 'Only pending requests can be rejected.');
        }

        $request->validate(['rejection_reason' => 'required|string|max:255']);

        try {
            DB::transaction(function () use ($request, $withdrawal) {
                $withdrawal->update([
                    'status' => WithdrawalStatus::REJECTED,
                    'rejection_reason' => $request->input('rejection_reason'),
                    'processed_by' => auth()->id(),
                    'processed_at' => now(),
                ]);

                // Refund the amount back to user's wallet
                $this->walletService->refund(
                    user: $withdrawal->user,
                    amount: (float) $withdrawal->amount,
                    reference: $withdrawal,
                    description: 'Refund for rejected withdrawal request #' . $withdrawal->id . ' (' . $request->input('rejection_reason') . ')'
                );
            });

            return back()->with('success', 'Withdrawal request rejected and funds returned to user wallet.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
