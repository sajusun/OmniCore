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
            'subject_type' => ['required_without:type', 'nullable', 'string'],
            'type' => ['required_without:subject_type', 'nullable', 'string'],
            'subject_id' => ['required_without:id', 'nullable'],
            'id' => ['required_without:subject_id', 'nullable'],
            'collection' => ['nullable', 'string', 'max:64'],
        ];
    }

    public function getTargetType(): ?string
    {
        $val = $this->input('subject_type') ?? $this->input('type');

        return $val !== null ? (string) $val : null;
    }

    public function getTargetId(): int|string|null
    {
        return $this->input('subject_id') ?? $this->input('id');
    }

    public function getCollection(): string
    {
        return (string) ($this->input('collection') ?? 'default');
    }
}
