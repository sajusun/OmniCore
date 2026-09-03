<?php

namespace App\Modules\Chat\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlockUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $userParam = $this->route('user');
        $userId = is_object($userParam) ? $userParam->id : $userParam;

        $this->merge([
            'blocked_user_id' => $userId,
        ]);
    }

    public function rules(): array
    {
        return [
            'blocked_user_id' => [
                'required',
                'integer',
                'exists:users,id',
                'different:' . auth('api')->id(),
            ],
        ];
    }
}
