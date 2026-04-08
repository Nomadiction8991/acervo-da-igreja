<?php

declare(strict_types=1);

namespace App\Http\Requests\DriveAccount;

use Illuminate\Foundation\Http\FormRequest;

final class StoreDriveAccountRequest extends FormRequest
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
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'folder_id' => [
                'nullable',
                'string',
                'max:255',
                static function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! is_string($value)) {
                        return;
                    }

                    if (str_starts_with(trim($value), '0A')) {
                        $fail('Use o ID de uma pasta do Google Drive, nao o ID do Shared Drive.');
                    }
                },
            ],
            'client_id' => ['nullable', 'string'],
            'client_secret' => ['nullable', 'string'],
            'refresh_token' => ['nullable', 'string'],
        ];
    }
}
