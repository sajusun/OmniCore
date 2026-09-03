<?php

namespace App\Modules\Call\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrackStateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_muted'          => ['required', 'boolean'],
            'is_video_enabled'  => ['required', 'boolean'],
            'is_screen_sharing' => ['required', 'boolean'],
        ];
    }
}
