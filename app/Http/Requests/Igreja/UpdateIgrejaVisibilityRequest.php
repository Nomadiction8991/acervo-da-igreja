<?php

declare(strict_types=1);

namespace App\Http\Requests\Igreja;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateIgrejaVisibilityRequest extends FormRequest
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
            'visibilidade' => ['required', 'array'],
            'visibilidade.codigo_controle' => ['nullable', 'boolean'],
            'visibilidade.nome_fantasia' => ['nullable', 'boolean'],
            'visibilidade.razao_social' => ['nullable', 'boolean'],
            'visibilidade.matricula' => ['nullable', 'boolean'],
            'visibilidade.cep' => ['nullable', 'boolean'],
            'visibilidade.endereco' => ['nullable', 'boolean'],
            'visibilidade.cidade' => ['nullable', 'boolean'],
            'visibilidade.estado' => ['nullable', 'boolean'],
        ];
    }
}
