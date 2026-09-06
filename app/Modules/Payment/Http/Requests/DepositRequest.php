<?php

namespace App\Modules\Payment\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepositRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:1', 'max:1000000'],
            'method' => ['required', 'string', 'in:stripe,paypal,sslcommerz,bkash,manual_bank'],
            'currency' => ['nullable', 'string', 'size:3'],
        ];
    }
}
