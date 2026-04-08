<?php

declare(strict_types=1);

namespace App\Http\Requests\Documento;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateDocumentoRequest extends FormRequest
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
            'igreja_id' => ['required', 'integer', Rule::exists('igrejas', 'id')->whereNull('deleted_at')],
            'grupo_documento_id' => ['nullable', 'integer', 'exists:grupo_documentos,id'],
            'drive_account_id' => ['nullable', 'integer', Rule::exists('drive_accounts', 'id')->whereNotNull('refresh_token')],
            'drive_folder_id' => [
                'nullable',
                'string',
                'max:255',
                static function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! is_string($value)) {
                        return;
                    }

                    if (str_starts_with(trim($value), '0A')) {
                        $fail('Use o ID de uma pasta dentro do Drive compartilhado, nao o ID do Shared Drive.');
                    }
                },
            ],
            'titulo' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
            'tipo' => ['required', 'string', 'max:100'],
            'publico' => ['nullable', 'boolean'],
            'arquivo' => ['nullable', 'file', 'max:15360', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,jpg,jpeg,png,gif'],
        ];
    }
}
