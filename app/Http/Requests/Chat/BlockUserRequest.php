<?php

namespace App\Http\Requests\Chat;

use Illuminate\Foundation\Http\FormRequest;

class BlockUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Extract the user parameter from the route, which could be an ID or a User model instance
        $userParam = $this->route('user');
        $userId = is_object($userParam) ? $userParam->id : $userParam;

        $this->merge([
            'blocked_user_id' => $userId,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
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
