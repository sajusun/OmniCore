<?php

namespace App\Http\Controllers\Web\Gateway\Stripe;

use Exception;
use Stripe\Stripe;
use Stripe\Transfer;
use App\Models\User;
use App\Http\Controllers\Controller;

class StripeTransferController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    public function transfer($user_id, $balance)
    {
        $user = User::find($user_id);

        if (!$user) {
            return redirect()->back()->with('t-error', 'User not found');
        }

        if (!$user->stripe_account_id) {
            return redirect()->back()->with('t-error', 'User does not have a Stripe account');
        }

        try {
            Transfer::create([
                'amount' => $balance * 100,
                'currency' => 'usd',
                'destination' => $user->stripe_account_id,
                'transfer_group' => 'transfer_group',
            ]);

            return redirect()->back()->with('t-success', 'Transfer successful');
        } catch (Exception $e) {
            return redirect()->back()->with('t-error', $e->getMessage());
        }

    }

}
