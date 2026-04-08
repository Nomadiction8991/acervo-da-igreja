<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Igreja;
use DomainException;
use Illuminate\Support\Facades\DB;

final class IgrejaService
{
    /**
     * @param array<string, mixed> $data
     */
    public function store(array $data): Igreja
    {
        /** @var Igreja */
        return DB::transaction(function () use ($data): Igreja {
            return Igreja::query()->create($this->normalizeData($data));
        });
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(Igreja $igreja, array $data): Igreja
    {
        /** @var Igreja */
        return DB::transaction(function () use ($igreja, $data): Igreja {
            $igreja->fill($this->normalizeData($data, $igreja));
            $igreja->save();

            return $igreja->refresh();
        });
    }

    /**
     * @param array<string, bool> $visibility
     */
    public function updateVisibility(Igreja $igreja, array $visibility): Igreja
    {
        /** @var Igreja */
        return DB::transaction(function () use ($igreja, $visibility): Igreja {
            foreach ($visibility as $field => $publico) {
                $igreja->definirCampoPublico($field, $publico);
            }

            $igreja->save();

            return $igreja->refresh();
        });
    }

    public function delete(Igreja $igreja): void
    {
        if (
            $igreja->fotos()->exists()
            || $igreja->documentos()->exists()
            || $igreja->tarefas()->exists()
        ) {
            throw new DomainException('Nao e possivel inativar a igreja enquanto houver fotos, documentos ou tarefas vinculados.');
        }

        DB::transaction(static function () use ($igreja): void {
            $igreja->delete();
        });
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function normalizeData(array $data, ?Igreja $igreja = null): array
    {
        foreach (Igreja::FIELD_VISIBILITY_MAP as $field => $column) {
            if (! array_key_exists($column, $data)) {
                $data[$column] = $igreja?->getAttribute($column) ?? $this->defaultVisibility($field);
            }
        }

        $visibilidade = [];

        foreach (Igreja::FIELD_VISIBILITY_MAP as $field => $column) {
            $visibilidade[$field] = (bool) $data[$column];
        }

        $data['visibilidade'] = $visibilidade;

        return $data;
    }

    private function defaultVisibility(string $field): bool
    {
        return match ($field) {
            'nome_fantasia', 'cep', 'endereco', 'cidade', 'estado' => true,
            default => false,
        };
    }
}
