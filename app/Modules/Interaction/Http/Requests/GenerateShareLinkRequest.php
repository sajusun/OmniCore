<?php

namespace App\Modules\Interaction\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateShareLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject_type' => ['required', 'string'],
            'subject_id' => ['required'],
            'type' => ['nullable', 'in:public,private,single_use,expiring'],
            'expires_in_minutes' => ['nullable', 'integer', 'min:1'],
            'max_clicks' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
