<?php

namespace App\Modules\Interaction\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
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
            'parent_id' => ['nullable', 'exists:comments,id'],
            'body' => ['required', 'string', 'min:1', 'max:5000'],
        ];
    }
}
