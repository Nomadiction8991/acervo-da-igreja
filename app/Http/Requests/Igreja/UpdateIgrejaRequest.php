<?php

declare(strict_types=1);

namespace App\Http\Requests\Igreja;

use App\Models\Igreja;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

final class UpdateIgrejaRequest extends FormRequest
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
            'codigo_controle' => ['required', 'string', 'max:20'],
            'nome_fantasia' => ['required', 'string', 'max:255'],
            'razao_social' => ['required', 'string', 'max:255'],
            'matricula' => ['nullable', 'string', 'max:50'],
            'cep' => ['nullable', 'string', 'max:10'],
            'endereco' => ['nullable', 'string', 'max:255'],
            'cidade' => ['nullable', 'string', 'max:100'],
            'estado' => ['nullable', 'string', 'max:2'],
            'publico_codigo_controle' => ['nullable', 'boolean'],
            'publico_nome_fantasia' => ['nullable', 'boolean'],
            'publico_razao_social' => ['nullable', 'boolean'],
            'publico_matricula' => ['nullable', 'boolean'],
            'publico_cep' => ['nullable', 'boolean'],
            'publico_endereco' => ['nullable', 'boolean'],
            'publico_cidade' => ['nullable', 'boolean'],
            'publico_estado' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            /** @var Igreja $igreja */
            $igreja = $this->route('igreja');
            $codigoControle = trim((string) $this->input('codigo_controle'));

            if ($codigoControle === '') {
                return;
            }

            $igrejaExistente = Igreja::query()
                ->withTrashed()
                ->where('codigo_controle', $codigoControle)
                ->whereKeyNot($igreja->id)
                ->first();

            if ($igrejaExistente === null) {
                return;
            }

            $validator->errors()->add(
                'codigo_controle',
                $igrejaExistente->trashed()
                    ? 'Já existe uma igreja inativa com este código de controle. Reative-a em vez de cadastrar outra.'
                    : 'Já existe uma igreja com este código de controle.'
            );
        });
    }
}
