<?php

namespace App\Modules\Post\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:5120'],

            'media' => ['nullable', 'array'],
            'media.*' => ['file', 'mimes:jpg,jpeg,png,webp,gif,mp4,mov,avi,mkv,webm', 'max:51200'],

            'visibility' => ['nullable'],
            'friend_ids' => ['nullable', 'array'],
            'friend_ids.*' => ['exists:users,id'],

            'type' => ['nullable'],
            'shared_post_id' => ['nullable', 'exists:posts,id'],
        ];
    }
}
