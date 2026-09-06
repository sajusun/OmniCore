<?php

declare(strict_types=1);

namespace App\Modules\Affiliate\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Affiliate\Enums\AffiliateStatus;
use App\Modules\Affiliate\Enums\CommissionType;
use App\Modules\Affiliate\Models\AffiliateAccount;
use App\Modules\Affiliate\Models\AffiliateCommission;
use App\Modules\Affiliate\Services\AffiliateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AffiliateAdminController extends Controller
{
    public function __construct(
        protected AffiliateService $affiliateService
    ) {}

    public function index(Request $request): View
    {
        $query = AffiliateAccount::with('user')->orderBy('total_earnings', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('referral_code', 'like', "%{$search}%")
                  ->orWhere('custom_slug', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $affiliates = $query->paginate(20)->withQueryString();

        $stats = [
            'total_affiliates'  => AffiliateAccount::count(),
            'active_affiliates' => AffiliateAccount::where('status', AffiliateStatus::ACTIVE->value)->count(),
            'total_paid_out'    => (float) AffiliateAccount::sum('paid_earnings'),
            'pending_balance'   => (float) AffiliateAccount::sum('current_balance'),
        ];

        return view('affiliate::backend.affiliates.index', compact('affiliates', 'stats'));
    }

    public function commissions(Request $request): View
    {
        $query = AffiliateCommission::with(['affiliate.user', 'referral'])->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $commissions = $query->paginate(20)->withQueryString();

        return view('affiliate::backend.commissions.index', compact('commissions'));
    }

    public function update(Request $request, AffiliateAccount $account): RedirectResponse
    {
        $validated = $request->validate([
            'commission_rate' => 'required|numeric|min:0|max:100',
            'commission_type' => ['required', Rule::enum(CommissionType::class)],
            'status'          => ['required', Rule::enum(AffiliateStatus::class)],
        ]);

        $account->update($validated);

        return back()->with('success', 'Affiliate account settings updated successfully.');
    }
}
