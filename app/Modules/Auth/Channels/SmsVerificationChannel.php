<?php

declare(strict_types=1);

namespace App\Modules\Auth\Channels;

use App\Modules\Auth\Contracts\VerificationChannelInterface;
use App\Modules\Auth\Models\Verification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class SmsVerificationChannel implements VerificationChannelInterface
{
    public function send(Model $verifiable, Verification $verification, string $type): void
    {
        $phone = $verifiable->phone ?? $verifiable->mobile ?? null;
        if (!$phone) {
            Log::warning("Cannot send SMS verification: Verifiable Model has no phone attribute.", [
                'model' => get_class($verifiable),
                'id'    => $verifiable->getKey(),
            ]);
            return;
        }

        // Standard SMS driver hook (e.g., Twilio, Vonage, BulkSMS, Firebase)
        Log::info("SMS Verification sent to [{$phone}]: Your code is {$verification->code}");
    }
}
