<?php

namespace App\Modules\Affiliate\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Affiliate\Models\AffiliateAccount;
use App\Modules\Affiliate\Models\AffiliateCommission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AffiliateAdminController extends Controller
{
    /**
     * Display all affiliate accounts.
     */
    public function index(Request $request): View
    {
        $query = AffiliateAccount::with('user')->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('referral_code', 'LIKE', "%{$search}%")
                  ->orWhere('custom_slug', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'LIKE', "%{$search}%")->orWhere('email', 'LIKE', "%{$search}%"));
            });
        }

        $affiliates = $query->paginate(15);
        $totalAffiliates = AffiliateAccount::count();
        $totalPaidCommissions = (float) AffiliateCommission::where('status', 'paid')->sum('commission_amount');
        $totalClicks = (int) AffiliateAccount::sum('lifetime_referrals');

        return view('affiliate_module::admin.index', compact(
            'affiliates',
            'totalAffiliates',
            'totalPaidCommissions',
            'totalClicks'
        ));
    }

    /**
     * Update affiliate custom commission or status.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $affiliate = AffiliateAccount::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|string|in:active,suspended',
            'custom_commission_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        $affiliate->update($validated);

        return redirect()->back()->with('success', 'Affiliate account updated successfully.');
    }

    /**
     * Commission conversions audit log.
     */
    public function commissions(): View
    {
        $commissions = AffiliateCommission::with(['affiliateAccount.user', 'order'])->latest()->paginate(20);

        return view('affiliate_module::admin.commissions.index', compact('commissions'));
    }
}
