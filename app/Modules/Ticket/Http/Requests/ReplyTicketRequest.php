<?php

declare(strict_types=1);

namespace App\Modules\Ticket\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReplyTicketRequest extends FormRequest
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
            'message'       => 'required|string',
            'attachments'   => 'nullable|array',
            'attachments.*' => 'file|max:10240|mimes:jpeg,png,jpg,gif,pdf,doc,docx,zip,txt',
        ];
    }
}
