<?php

namespace App\Http\Requests\Chat;

use Illuminate\Validation\Rule;
use App\Enums\Chat\MessageTypeEnum;
use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'chat_room_id' => 'required|integer|exists:chat_rooms,id',
            'message_type' => ['nullable', Rule::enum(MessageTypeEnum::class)],
            'message' => 'required_without:files|nullable|string|max:5000',
            'reply_to' => 'nullable|integer|exists:messages,id',
            'files' => 'nullable|array',
            'files.*' => 'file|max:20480', // limit to 20MB per file
        ];
    }
}
