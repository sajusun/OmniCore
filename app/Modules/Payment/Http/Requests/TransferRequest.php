<?php

namespace App\Modules\Payment\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'recipient_email' => ['required_without:recipient_id', 'nullable', 'email', 'exists:users,email'],
            'recipient_id' => ['required_without:recipient_email', 'nullable', 'integer', 'exists:users,id'],
            'amount' => ['required', 'numeric', 'min:0.5', 'max:50000'],
            'note' => ['nullable', 'string', 'max:255'],
        ];
    }
}
