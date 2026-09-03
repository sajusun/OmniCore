<?php

namespace App\Modules\Chat\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForwardMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'message_ids'       => ['required', 'array', 'min:1'],
            'message_ids.*'     => ['required', 'integer', 'exists:messages,id'],
            'target_room_ids'   => ['required', 'array', 'min:1'],
            'target_room_ids.*' => ['required', 'integer', 'exists:chat_rooms,id'],
        ];
    }
}
