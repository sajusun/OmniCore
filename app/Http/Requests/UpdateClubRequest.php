<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClubRequest extends FormRequest
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
            'club_name'   => 'nullable|string|max:255',
            'name'        => 'nullable|string|max:255',
            'club_type'   => 'nullable|string|in:car,motorcycle',
            'type'        => 'nullable|string|in:car,motorcycle',
            'country'     => 'nullable|string|max:100',
            'state'       => 'nullable|string|max:100',
            'city'        => 'nullable|string|max:100',
            'description' => 'nullable|string|max:5000',
            'status'      => 'nullable|string|in:draft,published,pending',
            
            // Media fields
            'thumbnail'   => 'nullable|file|image|max:10240', // 10MB limit
            'images'      => 'nullable|array',
            'images.*'    => 'file|image|max:10240',
            'video'       => 'nullable|array',
            'video.*'     => 'file|mimetypes:video/mp4,video/quicktime,video/x-msvideo|max:51200', // 50MB limit
            'videos'      => 'nullable|array',
            'videos.*'    => 'file|mimetypes:video/mp4,video/quicktime,video/x-msvideo|max:51200',
        ];
    }
}
