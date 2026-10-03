<?php

declare(strict_types=1);

namespace App\Modules\Review\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'reviewable_type'  => 'required|string',
            'reviewable_id'    => 'required|integer',
            'rating'           => 'required|integer|min:1|max:5',
            'title'            => 'nullable|string|max:255',
            'comment'          => 'required|string|min:5',
            'criteria_ratings' => 'nullable|array',
            'media'            => 'nullable|array',
            'media.*'          => 'file|max:10240|mimes:jpeg,png,jpg,gif,mp4,mov,avi,webp',
        ];
    }
}
