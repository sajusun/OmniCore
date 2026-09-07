<?php

namespace App\Modules\Payment\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Modules\Payment\Enums\PaymentStatus;
use App\Modules\Payment\Models\Payment;
use App\Modules\Payment\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentHistoryController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {
        parent::__construct();
    }

    public function index(Request $request): View
    {
        $payments = Payment::with(['user', 'payable'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('method'), fn($q) => $q->where('method', $request->input('method')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->input('search');
                $q->where(function ($sub) use ($search) {
                    $sub->where('payment_id', 'like', "%{$search}%")
                        ->orWhere('gateway_transaction_id', 'like', "%{$search}%")
                        ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(20);

        $stats = [
            'total_payments' => Payment::count(),
            'total_revenue' => (float) Payment::completed()->sum('amount'),
            'completed_count' => Payment::completed()->count(),
            'pending_count' => Payment::pending()->count(),
        ];

        return view('payment::backend.payments.index', compact('payments', 'stats'));
    }

    public function show(Payment $payment): View
    {
        $payment->load(['user', 'payable']);
        return view('payment::backend.payments.show', compact('payment'));
    }

    public function markPaid(Payment $payment): RedirectResponse
    {
        try {
            $this->paymentService->markAsCompleted($payment, 'ADMIN_MANUAL_' . auth()->id());
            return back()->with('success', 'Payment marked as completed successfully');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function refund(Request $request, Payment $payment): RedirectResponse
    {
        $request->validate(['reason' => 'nullable|string|max:255']);

        try {
            $this->paymentService->refundPayment($payment, null, $request->input('reason', 'Admin requested refund'));
            return back()->with('success', 'Payment refunded successfully');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
