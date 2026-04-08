<?php

declare(strict_types=1);

namespace App\Http\Requests\GrupoDocumento;

use App\Models\GrupoDocumento;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateGrupoDocumentoRequest extends FormRequest
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
        /** @var GrupoDocumento $grupoDocumento */
        $grupoDocumento = $this->route('grupo_documento');

        return [
            'nome' => ['required', 'string', 'max:255', Rule::unique('grupo_documentos', 'nome')->ignore($grupoDocumento->id)],
            'descricao' => ['nullable', 'string'],
            'publico_padrao' => ['nullable', 'boolean'],
        ];
    }
}
