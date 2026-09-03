<?php

namespace App\Modules\AppSupport\Http\Requests;

use App\Modules\AppSupport\Enums\SupportCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CreateSupportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject' => 'required|string|max:255',
            'category' => ['required', new Enum(SupportCategory::class)],
            'message' => 'required|string|max:5000',
            'device_os' => 'nullable|string|max:100',
            'device_model' => 'nullable|string|max:100',
            'app_version' => 'nullable|string|max:50',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:jpeg,png,jpg,webp,gif,pdf|max:10240', // 10MB max per file
        ];
    }
}
