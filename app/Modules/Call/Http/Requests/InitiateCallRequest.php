<?php

namespace App\Modules\Call\Http\Requests;

use App\Modules\Call\Enums\CallTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InitiateCallRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'receiver_id'   => ['required_without:participant_ids', 'nullable', 'integer', 'exists:users,id'],
            'participant_ids' => ['required_without:receiver_id', 'nullable', 'array', 'min:1'],
            'participant_ids.*' => ['integer', 'exists:users,id'],
            'chat_room_id'  => ['nullable', 'integer', 'exists:chat_rooms,id'],
            'type'          => ['required', Rule::enum(CallTypeEnum::class)],
        ];
    }
}
