<?php

namespace App\Modules\Subscription\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubscribeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'plan_id' => ['required', 'integer', 'exists:plans,id'],
            'payment_method' => ['nullable', 'string', 'in:wallet,stripe,paypal,sslcommerz,bkash'],
            'auto_renew' => ['nullable', 'boolean'],
        ];
    }
}
