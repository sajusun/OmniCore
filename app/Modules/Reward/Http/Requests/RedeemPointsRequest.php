<?php

namespace App\Modules\Reward\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RedeemPointsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'points' => ['required', 'integer', 'min:100', 'max:1000000'],
        ];
    }
}
