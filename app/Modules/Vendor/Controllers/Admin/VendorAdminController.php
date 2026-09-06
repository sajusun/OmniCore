<?php

declare(strict_types=1);

namespace App\Modules\Vendor\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Vendor\Enums\PayoutStatus;
use App\Modules\Vendor\Enums\VendorStatus;
use App\Modules\Vendor\Models\VendorPayout;
use App\Modules\Vendor\Models\VendorStore;
use App\Modules\Vendor\Services\VendorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VendorAdminController extends Controller
{
    public function __construct(
        protected VendorService $vendorService
    ) {}

    public function index(Request $request): View
    {
        $query = VendorStore::with('owner')->orderBy('total_sales', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('owner', function ($oq) use ($search) {
                      $oq->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $stores = $query->paginate(20)->withQueryString();

        $stats = [
            'total_stores'  => VendorStore::count(),
            'active_stores' => VendorStore::where('status', VendorStatus::ACTIVE->value)->count(),
            'total_volume'  => (float) VendorStore::sum('total_sales'),
            'total_payouts' => (float) VendorPayout::where('status', PayoutStatus::COMPLETED->value)->sum('amount'),
        ];

        return view('vendor::backend.vendors.index', compact('stores', 'stats'));
    }

    public function update(Request $request, VendorStore $store): RedirectResponse
    {
        $validated = $request->validate([
            'commission_rate' => 'required|numeric|min:0|max:100',
            'status'          => ['required', Rule::enum(VendorStatus::class)],
            'is_featured'     => 'nullable|boolean',
        ]);

        $validated['is_featured'] = $request->boolean('is_featured');

        $this->vendorService->updateStatus($store, VendorStatus::from($validated['status']));
        $store->update([
            'commission_rate' => $validated['commission_rate'],
            'is_featured'     => $validated['is_featured'],
        ]);

        return back()->with('success', 'Store settings updated successfully.');
    }

    public function payouts(Request $request): View
    {
        $query = VendorPayout::with(['store.owner', 'requester'])->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payouts = $query->paginate(20)->withQueryString();

        return view('vendor::backend.payouts.index', compact('payouts'));
    }

    public function processPayout(Request $request, VendorPayout $payout): RedirectResponse
    {
        $request->validate([
            'status'     => ['required', Rule::enum(PayoutStatus::class)],
            'admin_note' => 'nullable|string',
        ]);

        $status = PayoutStatus::from($request->status);

        $payout->update([
            'status'       => $status->value,
            'admin_note'   => $request->admin_note,
            'processed_at' => $status === PayoutStatus::COMPLETED ? now() : $payout->processed_at,
        ]);

        return back()->with('success', 'Payout request updated.');
    }
}
