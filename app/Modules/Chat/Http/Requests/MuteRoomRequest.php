<?php

namespace App\Modules\Chat\Http\Requests;

use App\Modules\Chat\Enums\MuteDurationEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MuteRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'duration'   => ['required', Rule::enum(MuteDurationEnum::class)],
            'mute_until' => ['required_if:duration,custom', 'nullable', 'date', 'after:now'],
        ];
    }
}
