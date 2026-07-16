<?php

namespace App\Http\Controllers\Api\Webhooks;

use App\Models\User;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class RevenueCatWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        $authorizationHeader = $request->header('Authorization');
        $expectedToken = config('services.revenuecat.webhook_secret');

        if (!$authorizationHeader || !hash_equals($expectedToken, $authorizationHeader)) {
            Log::warning('Unauthorized RevenueCat webhook request.');

            return response()->json(['status'=>false,'message' => 'Unauthorized'], 401);
        }

        Log::info('RevenueCat Webhook', $request->all());

        $event = $request->input('event');

        switch ($event['type']) {

            case 'INITIAL_PURCHASE':
            case 'RENEWAL':
                $this->handleSubscription($event);
                break;

            case 'EXPIRATION':
            case 'CANCELLATION':
                $this->handleCancellation($event);
                break;
        }

        return response()->json([
            'status' => true,
            'message' => "Subscription Pass"
        ]);
    }
    private function handleSubscription(array $event): void
    {
        $appUserId = $event['app_user_id'];

        $user = User::find($appUserId);

        if (!$user) {
            return;
        }

        Subscription::updateOrCreate(
            [
                'user_id' => $user->id,
                'product_id' => $event['product_id'],
            ],
            [
                'revenuecat_id' => $appUserId,
                // 'package' => $event['package_id'] ?? null,
                'package' => $event['period_type'] ?? null,
                'is_active' => true,
                'started_at' => now(),
                'expires_at' => Carbon::createFromTimestampMs(
                    $event['expiration_at_ms']
                ),
                'meta' => $event,
            ]
        );

        $user->update([
            'is_premium' => 1,
            'subscription_ends_at' => Carbon::createFromTimestampMs(
                $event['expiration_at_ms']
            ),
        ]);
    }

    private function handleCancellation(array $event): void
    {
        $user = User::find($event['app_user_id']);

        if (!$user) {
            return;
        }

        Subscription::where('user_id', $user->id)->where('product_id', $event['product_id'])
            ->update([
                'is_active' => false,
            ]);

        $user->update([
            'is_premium' => 0,
        ]);
    }
}
