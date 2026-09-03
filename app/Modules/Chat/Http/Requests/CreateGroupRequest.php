<?php

namespace App\Modules\Chat\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string|max:1000',
            'image'             => 'nullable|string|max:500',
            'participant_ids'   => 'required|array|min:1',
            'participant_ids.*' => [
                'integer',
                'exists:users,id',
                'different:' . auth('api')->id(),
            ],
        ];
    }
}
