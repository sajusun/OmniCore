<?php

namespace App\Modules\Payment\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawalRequestForm extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:5', 'max:100000'],
            'method' => ['required', 'string', 'max:50'],
            'account_details' => ['required', 'array'],
            'account_details.account_number' => ['required', 'string'],
            'account_details.account_name' => ['nullable', 'string'],
            'account_details.bank_name' => ['nullable', 'string'],
            'account_details.branch' => ['nullable', 'string'],
        ];
    }
}
