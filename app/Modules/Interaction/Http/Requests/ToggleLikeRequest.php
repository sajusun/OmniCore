<?php

namespace App\Modules\Interaction\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ToggleLikeRequest extends FormRequest
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
            'type' => ['nullable', 'string', 'max:32'],
        ];
    }
}
