<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\TaskStatus;
use App\Models\Tarefa;
use Illuminate\Support\Facades\DB;

final class TarefaService
{
    /**
     * @param array<string, mixed> $data
     */
    public function store(array $data): Tarefa
    {
        /** @var Tarefa */
        return DB::transaction(function () use ($data): Tarefa {
            return Tarefa::query()->create($this->normalize($data));
        });
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(Tarefa $tarefa, array $data): Tarefa
    {
        /** @var Tarefa */
        return DB::transaction(function () use ($tarefa, $data): Tarefa {
            $tarefa->update($this->normalize($data));

            return $tarefa->refresh();
        });
    }

    public function delete(Tarefa $tarefa): void
    {
        DB::transaction(static function () use ($tarefa): void {
            $tarefa->delete();
        });
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function normalize(array $data): array
    {
        /** @var string $status */
        $status = $data['status'] ?? TaskStatus::Pendente->value;

        $data['completed_at'] = $status === TaskStatus::Concluida->value
            ? ($data['completed_at'] ?? now())
            : null;

        return $data;
    }
}
