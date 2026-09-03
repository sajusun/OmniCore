<?php

namespace App\Modules\Call\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SignalingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'      => ['required', 'string', 'in:offer,answer,ice_candidate'],
            'payload'   => ['required', 'array'],
            'target_id' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }
}
