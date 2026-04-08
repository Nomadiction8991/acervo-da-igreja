<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\GrupoDocumento;
use Illuminate\Support\Facades\DB;

final class GrupoDocumentoService
{
    /**
     * @param array<string, mixed> $data
     */
    public function store(array $data): GrupoDocumento
    {
        /** @var GrupoDocumento */
        return DB::transaction(static fn (): GrupoDocumento => GrupoDocumento::query()->create($data));
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(GrupoDocumento $grupoDocumento, array $data): GrupoDocumento
    {
        /** @var GrupoDocumento */
        return DB::transaction(function () use ($grupoDocumento, $data): GrupoDocumento {
            $grupoDocumento->update($data);

            return $grupoDocumento->refresh();
        });
    }

    public function delete(GrupoDocumento $grupoDocumento): void
    {
        DB::transaction(static function () use ($grupoDocumento): void {
            $grupoDocumento->delete();
        });
    }
}
