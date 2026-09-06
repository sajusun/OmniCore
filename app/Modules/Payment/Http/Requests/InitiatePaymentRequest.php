<?php

namespace App\Modules\Payment\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InitiatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payable_type' => ['required', 'string'],
            'payable_id' => ['required', 'integer'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'method' => ['required', 'string', 'in:stripe,paypal,sslcommerz,bkash,wallet,manual_bank,cod'],
            'currency' => ['nullable', 'string', 'size:3'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
