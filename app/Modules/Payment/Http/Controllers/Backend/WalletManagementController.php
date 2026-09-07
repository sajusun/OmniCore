<?php

namespace App\Modules\Payment\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Modules\Payment\Models\Wallet;
use App\Modules\Payment\Models\WalletTransaction;
use App\Modules\Payment\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WalletManagementController extends Controller
{
    public function __construct(
        protected WalletService $walletService
    ) {
        parent::__construct();
    }

    public function index(Request $request): View
    {
        $wallets = Wallet::with('user')
            ->when($request->filled('frozen'), fn($q) => $q->where('status', $request->boolean('frozen') ? 'locked' : 'active'))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->input('search');
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            })
            ->latest('balance')
            ->paginate(20);

        $stats = [
            'total_wallets' => Wallet::count(),
            'total_circulation' => (float) Wallet::sum('balance'),
            'frozen_wallets' => Wallet::where('status', 'locked')->count(),
        ];

        return view('payment::backend.wallets.index', compact('wallets', 'stats'));
    }

    public function transactions(Request $request): View
    {
        $transactions = WalletTransaction::with(['user', 'wallet'])
            ->when($request->filled('type'), fn($q) => $q->where('type', $request->input('type')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->input('search');
                $q->where('trx_id', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate(25);

        return view('payment::backend.wallets.transactions', compact('transactions'));
    }

    public function toggleFreeze(Wallet $wallet): RedirectResponse
    {
        $wallet->status = $wallet->status === 'locked' ? 'active' : 'locked';
        $wallet->save();

        $status = $wallet->status === 'locked' ? 'frozen' : 'unfrozen';
        return back()->with('success', "Wallet has been {$status} successfully.");
    }

    public function adjustBalance(Request $request, Wallet $wallet): RedirectResponse
    {
        $request->validate([
            'type' => 'required|in:credit,debit',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:255',
        ]);

        try {
            $this->walletService->adjustBalance(
                user: $wallet->user,
                amount: (float) $request->input('amount'),
                type: $request->input('type'),
                reason: $request->input('reason'),
                admin: auth()->user()
            );

            return back()->with('success', 'Wallet balance adjusted successfully.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
