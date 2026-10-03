<?php

declare(strict_types=1);

namespace App\Modules\Vendor\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestVendorPayoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:1',
            'method' => 'nullable|string|in:wallet,bank_transfer',
        ];
    }
}
