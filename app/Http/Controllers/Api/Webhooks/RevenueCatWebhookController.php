<?php

namespace App\Http\Controllers\Api\Webhooks;

use App\Models\User;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;

class RevenueCatWebhookController extends Controller
{
    protected User $user;
    protected Subscription $subscription;
    protected mixed $event;
    public function __construct(Request $request)
    {
        $this->event = $request->input('event');
        $this->user = User::findOrFail($this->event['app_user_id']);
    }

    public function __invoke(Request $request)
    {
        $authorizationHeader = $request->header('Authorization');
        $expectedToken = config('services.revenuecat.webhook_secret');

        if (!$authorizationHeader || !hash_equals($expectedToken, $authorizationHeader)) {

            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        // $event = $this->event;

        switch ($this->event['type']) {

            case 'INITIAL_PURCHASE':
            case 'RENEWAL':
                $this->handleSubscription();
                break;

            case 'EXPIRATION':
            case 'CANCELLATION':
                $this->handleCancellation();
                break;
        }

        return response()->json([
            'status' => true,
            'message' => "Webhook processed successfully"
        ]);
    }
    private function handleSubscription(): void
    {
        $appUserId = $this->event['app_user_id'];

        if (!$this->user) {
            return;
        }

        Subscription::updateOrCreate(
            [
                'user_id' => $this->user->id,
                'product_id' => $this->event['product_id'],
            ],
            [
                'revenuecat_id' => $appUserId,
                'package' => $this->event['period_type'] ?? null,
                'is_active' => true,
                'started_at' => now(),
                'expires_at' => Carbon::createFromTimestampMs(
                    $this->event['expiration_at_ms']
                ),
                'meta' => $this->event,
            ]
        );

        $this->user->update([
            'is_subscribed' => 1,
            'subscription_ends_at' => Carbon::createFromTimestampMs(
                $this->event['expiration_at_ms']
            ),
        ]);
    }

    private function handleCancellation(): void
    {


        if (!$this->user) {
            return;
        }

        Subscription::where('user_id', $this->user->id)->where('product_id', $this->event['product_id'])
            ->update([
                'is_active' => false,
            ]);

        $this->user->update([
            'is_subscribed' => 0,
        ]);
    }
}
