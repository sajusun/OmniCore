<?php

declare(strict_types=1);

namespace App\Modules\Ticket\Http\Requests;

use App\Modules\Ticket\Enums\TicketPriority;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTicketRequest extends FormRequest
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
            'subject'       => 'required|string|max:255',
            'category_id'   => 'nullable|exists:ticket_categories,id',
            'priority'      => ['nullable', Rule::enum(TicketPriority::class)],
            'message'       => 'required|string',
            'attachments'   => 'nullable|array',
            'attachments.*' => 'file|max:10240|mimes:jpeg,png,jpg,gif,pdf,doc,docx,zip,txt',
        ];
    }
}
