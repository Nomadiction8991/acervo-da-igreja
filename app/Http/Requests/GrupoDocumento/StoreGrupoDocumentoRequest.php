<?php

declare(strict_types=1);

namespace App\Http\Requests\GrupoDocumento;

use Illuminate\Foundation\Http\FormRequest;

final class StoreGrupoDocumentoRequest extends FormRequest
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
            'nome' => ['required', 'string', 'max:255', 'unique:grupo_documentos,nome'],
            'descricao' => ['nullable', 'string'],
            'publico_padrao' => ['nullable', 'boolean'],
        ];
    }
}
