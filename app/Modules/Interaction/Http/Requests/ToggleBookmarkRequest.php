<?php

namespace App\Modules\Interaction\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ToggleBookmarkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject_type' => ['sometimes', 'required_without:type', 'string'],
            'type' => ['sometimes', 'required_without:subject_type', 'string'],
            'subject_id' => ['sometimes', 'required_without:id'],
            'id' => ['sometimes', 'required_without:subject_id'],
            'collection' => ['nullable', 'string', 'max:64'],
        ];
    }

    public function getTargetType(): string
    {
        return (string) ($this->input('subject_type') ?? $this->input('type'));
    }

    public function getTargetId(): int|string
    {
        return $this->input('subject_id') ?? $this->input('id');
    }

    public function getCollection(): string
    {
        return (string) ($this->input('collection') ?? 'default');
    }
}
