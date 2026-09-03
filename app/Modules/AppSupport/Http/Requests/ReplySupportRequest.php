<?php

namespace App\Modules\AppSupport\Http\Requests;

use App\Modules\AppSupport\Enums\SupportStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class ReplySupportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'message' => 'required|string|max:5000',
            'status'  => ['nullable', new Enum(SupportStatus::class)],
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:jpeg,png,jpg,webp,gif,pdf|max:10240',
        ];
    }
}
