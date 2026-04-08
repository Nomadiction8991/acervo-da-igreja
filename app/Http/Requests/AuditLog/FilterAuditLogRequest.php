<?php

declare(strict_types=1);

namespace App\Http\Requests\AuditLog;

use Illuminate\Foundation\Http\FormRequest;

final class FilterAuditLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'acao' => ['nullable', 'string', 'max:100'],
            'modulo' => ['nullable', 'string', 'max:100'],
            'data_inicial' => ['nullable', 'date'],
            'data_final' => ['nullable', 'date'],
        ];
    }
}
