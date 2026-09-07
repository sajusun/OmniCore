<?php

namespace App\Modules\Vendor\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Vendor\Models\VendorPayout;
use App\Modules\Vendor\Models\VendorStore;
use App\Modules\Vendor\Services\VendorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VendorAdminController extends Controller
{
    public function __construct(protected VendorService $vendorService)
    {
    }

    /**
     * Display all vendor stores.
     */
    public function stores(Request $request): View
    {
        $query = VendorStore::with('user')->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $stores = $query->paginate(15);
        $totalStores = VendorStore::count();
        $verifiedStores = VendorStore::where('is_verified', true)->count();
        $totalRevenue = VendorStore::sum('total_sales');

        return view('vendor_module::admin.stores.index', compact(
            'stores',
            'totalStores',
            'verifiedStores',
            'totalRevenue'
        ));
    }

    /**
     * Update vendor store status and commission.
     */
    public function updateStore(Request $request, int $id): RedirectResponse
    {
        $store = VendorStore::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|string|in:active,suspended,pending',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'is_verified' => 'nullable|boolean',
        ]);

        $store->update([
            'status' => $validated['status'],
            'commission_rate' => $validated['commission_rate'] ?? $store->commission_rate,
            'is_verified' => $request->has('is_verified'),
        ]);

        return redirect()->back()->with('success', "Store '{$store->name}' updated successfully.");
    }

    /**
     * Display all vendor payout requests.
     */
    public function payouts(Request $request): View
    {
        $query = VendorPayout::with(['vendorStore.user'])->latest();

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $payouts = $query->paginate(15);
        $pendingAmount = VendorPayout::where('status', 'pending')->sum('amount');
        $completedAmount = VendorPayout::where('status', 'completed')->sum('amount');

        return view('vendor_module::admin.payouts.index', compact(
            'payouts',
            'pendingAmount',
            'completedAmount'
        ));
    }

    /**
     * Approve payout request.
     */
    public function approvePayout(Request $request, int $id): RedirectResponse
    {
        $payout = VendorPayout::findOrFail($id);

        if ($payout->status !== 'pending') {
            return redirect()->back()->with('error', 'Payout request is already processed.');
        }

        $payout->update([
            'status' => 'completed',
            'processed_at' => now(),
            'notes' => $request->input('notes', 'Approved by administrator.'),
        ]);

        return redirect()->back()->with('success', 'Vendor payout approved successfully.');
    }

    /**
     * Reject payout request and refund vendor balance.
     */
    public function rejectPayout(Request $request, int $id): RedirectResponse
    {
        $payout = VendorPayout::with('vendorStore')->findOrFail($id);

        if ($payout->status !== 'pending') {
            return redirect()->back()->with('error', 'Payout request is already processed.');
        }

        // Refund balance to vendor
        $payout->vendorStore->increment('balance', $payout->amount);

        $payout->update([
            'status' => 'rejected',
            'processed_at' => now(),
            'notes' => $request->input('notes', 'Rejected by administrator.'),
        ]);

        return redirect()->back()->with('success', 'Vendor payout rejected and balance refunded to store.');
    }
}
