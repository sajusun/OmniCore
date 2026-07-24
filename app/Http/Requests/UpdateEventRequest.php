<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'club_id'           => 'nullable|integer|exists:clubs,id',
            'title'             => 'nullable|string|max:255',
            'description'       => 'nullable|string|max:5000',
            'event_type'        => 'nullable',
            'location'          => 'nullable|string|max:255',
            'latitude'          => 'nullable|numeric|between:-90,90',
            'longitude'         => 'nullable|numeric|between:-180,180',
            'event_date'        => 'nullable|date',
            'event_time'        => 'nullable|string',
            'max_participants'  => 'nullable|integer|min:1',
            'vehicles_required' => 'nullable|array',
            'vehicles_required.*' => 'integer',
            'is_public'         => 'nullable|boolean',
            'status'            => 'nullable|string|in:draft,published,cancelled,completed',

            // Media
            'thumbnail'         => 'nullable|file|image|max:10240',
            'images'            => 'nullable|array',
            'images.*'          => 'file|image|max:10240',
        ];
    }
}
