<?php

namespace App\Modules\Payment\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Payment\Models\Payment;
use App\Modules\Payment\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {
        parent::__construct();
    }

    /**
     * Handle incoming gateway IPN / Webhooks
     */
    public function handle(Request $request, string $gateway): JsonResponse
    {
        Log::info("Payment webhook received for [{$gateway}]", $request->all());

        try {
            $paymentId = $request->input('payment_id')
                ?? $request->input('tran_id')
                ?? $request->input('client_reference_id')
                ?? $request->input('data.object.client_reference_id');

            if ($paymentId) {
                $payment = Payment::where('payment_id', $paymentId)
                    ->orWhere('gateway_transaction_id', $paymentId)
                    ->first();

                if ($payment) {
                    $this->paymentService->verifyPayment($payment, $request->all());
                }
            }

            return response()->json(['status' => 'success', 'message' => 'Webhook processed'], 200);
        } catch (\Throwable $e) {
            Log::error("Webhook error for [{$gateway}]: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }
}
