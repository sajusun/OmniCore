<?php

declare(strict_types=1);

namespace App\Modules\Auth\Contracts;

use App\Modules\Auth\Models\Verification;
use Illuminate\Database\Eloquent\Model;

interface VerificationChannelInterface
{
    /**
     * Deliver the verification code / token to the verifiable model.
     *
     * @param Model $verifiable
     * @param Verification $verification
     * @param string $type
     * @return void
     */
    public function send(Model $verifiable, Verification $verification, string $type): void;
}
