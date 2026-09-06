<?php

namespace App\Http\Controllers\Api\Webhooks;

use App\Models\User;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;

class RevenueCatWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        $authorizationHeader = $request->header('Authorization');
        $expectedToken = config('services.revenuecat.webhook_secret');

        if (!$expectedToken || !$authorizationHeader || !hash_equals($expectedToken, $authorizationHeader)) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $event = $request->input('event');
        if (!is_array($event) || empty($event['app_user_id'])) {
            return response()->json(['status' => false, 'message' => 'Invalid event payload'], 400);
        }

        $user = User::find($event['app_user_id']);
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not found'], 404);
        }

        switch ($event['type'] ?? null) {
            case 'INITIAL_PURCHASE':
            case 'RENEWAL':
                $this->handleSubscription($user, $event);
                break;

            case 'EXPIRATION':
            case 'CANCELLATION':
                $this->handleCancellation($user, $event);
                break;
        }

        return response()->json([
            'status' => true,
            'message' => 'Webhook processed successfully',
        ]);
    }
    private function handleSubscription(User $user, array $event): void
    {
        $appUserId = $event['app_user_id'];
        $expirationAtMs = $event['expiration_at_ms'] ?? null;
        $expiresAt = $expirationAtMs ? Carbon::createFromTimestampMs($expirationAtMs) : null;

        Subscription::updateOrCreate(
            [
                'user_id' => $user->id,
                'product_id' => $event['product_id'] ?? null,
            ],
            [
                'revenuecat_id' => $appUserId,
                'package' => $event['period_type'] ?? null,
                'is_active' => true,
                'started_at' => now(),
                'expires_at' => $expiresAt,
                'meta' => $event,
            ]
        );

        $user->update([
            'is_subscribed' => 1,
            'subscription_ends_at' => $expiresAt,
        ]);
    }

    private function handleCancellation(User $user, array $event): void
    {
        Subscription::where('user_id', $user->id)
            ->where('product_id', $event['product_id'] ?? null)
            ->update([
                'is_active' => false,
            ]);

        $user->update([
            'is_subscribed' => 0,
        ]);
    }
}
