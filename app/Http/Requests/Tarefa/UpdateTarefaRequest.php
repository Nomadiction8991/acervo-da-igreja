<?php

declare(strict_types=1);

namespace App\Http\Requests\Tarefa;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateTarefaRequest extends FormRequest
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
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'titulo' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
            'status' => ['required', 'string', Rule::in(array_column(TaskStatus::cases(), 'value'))],
            'prioridade' => ['required', 'string', Rule::in(array_column(TaskPriority::cases(), 'value'))],
            'due_at' => ['nullable', 'date'],
        ];
    }
}
