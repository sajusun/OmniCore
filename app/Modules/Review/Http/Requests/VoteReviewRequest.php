<?php

declare(strict_types=1);

namespace App\Modules\Review\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VoteReviewRequest extends FormRequest
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
            'is_helpful' => 'required|boolean',
        ];
    }
}
