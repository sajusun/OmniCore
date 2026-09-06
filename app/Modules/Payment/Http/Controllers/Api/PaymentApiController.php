<?php

namespace App\Modules\Payment\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Payment\Enums\PaymentMethod;
use App\Modules\Payment\Http\Requests\InitiatePaymentRequest;
use App\Modules\Payment\Http\Resources\GatewayResource;
use App\Modules\Payment\Http\Resources\PaymentResource;
use App\Modules\Payment\Models\Payment;
use App\Modules\Payment\Models\PaymentGateway;
use App\Modules\Payment\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentApiController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {
        parent::__construct();
    }

    /**
     * List active payment gateways
     */
    public function gateways(): JsonResponse
    {
        $gateways = PaymentGateway::active()->get();
        return $this->success(
            GatewayResource::collection($gateways),
            'Available payment gateways retrieved successfully'
        );
    }

    /**
     * User's payment history
     */
    public function index(Request $request): JsonResponse
    {
        $userId = auth('api')->id() ?? auth()->id();
        $payments = Payment::where('user_id', $userId)
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return $this->paginated($payments, PaymentResource::class, 'Payments history retrieved successfully');
    }

    /**
     * Single payment details
     */
    public function show(string $paymentId): JsonResponse
    {
        $userId = auth('api')->id() ?? auth()->id();
        $payment = Payment::where('user_id', $userId)
            ->where(function ($q) use ($paymentId) {
                $q->where('payment_id', $paymentId)->orWhere('id', $paymentId);
            })
            ->firstOrFail();

        return $this->success(new PaymentResource($payment), 'Payment details retrieved');
    }

    /**
     * Initiate payment for an order or payable item
     */
    public function initiate(InitiatePaymentRequest $request): JsonResponse
    {
        $user = auth('api')->user() ?? auth()->user();
        if (!$user) {
            return $this->error('Unauthenticated', 401);
        }

        $payableClass = $request->input('payable_type');
        // Map short alias or full class
        if ($payableClass === 'order' || $payableClass === 'Order') {
            $payableClass = \App\Modules\Order\Models\Order::class;
        }

        if (!class_exists($payableClass)) {
            return $this->error('Invalid payable item type', 422);
        }

        $payable = $payableClass::findOrFail($request->input('payable_id'));

        try {
            $method = PaymentMethod::from($request->input('method'));
            $payment = $this->paymentService->initiatePayment(
                user: $user,
                payable: $payable,
                amount: (float) $request->input('amount'),
                method: $method,
                options: [
                    'currency' => $request->input('currency', 'USD'),
                    'metadata' => $request->input('metadata', []),
                ]
            );

            return $this->success([
                'payment' => new PaymentResource($payment),
                'payment_url' => $payment->payment_url,
                'status' => $payment->status instanceof \App\Modules\Payment\Enums\PaymentStatus ? $payment->status->value : (string) $payment->status,
            ], 'Payment initiated successfully');
        } catch (\Throwable $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Verify payment return callback / redirect
     */
    public function verify(Request $request, string $paymentId): JsonResponse
    {
        try {
            $payment = Payment::where('payment_id', $paymentId)->orWhere('id', $paymentId)->firstOrFail();
            $updatedPayment = $this->paymentService->verifyPayment($payment, $request->all());

            return $this->success(
                new PaymentResource($updatedPayment),
                $updatedPayment->isCompleted() ? 'Payment completed successfully' : 'Payment verification failed'
            );
        } catch (\Throwable $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Cancel payment return callback
     */
    public function cancel(Request $request, string $paymentId): JsonResponse
    {
        $payment = Payment::where('payment_id', $paymentId)->orWhere('id', $paymentId)->firstOrFail();
        $this->paymentService->markAsFailed($payment, $request->input('reason', 'Cancelled by user'));

        return $this->success(new PaymentResource($payment), 'Payment was cancelled');
    }
}
