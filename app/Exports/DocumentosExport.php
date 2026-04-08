<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Documento;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

final class DocumentosExport implements FromCollection, WithHeadings
{
    public function collection(): Collection
    {
        return Documento::query()
            ->with('igreja')
            ->select('titulo', 'descricao', 'tipo', 'tamanho', 'publico', 'sync_status', 'created_at')
            ->orderByDesc('created_at')
            ->get()
            ->map(function (Documento $doc) {
                return [
                    'titulo' => $doc->titulo,
                    'descricao' => $doc->descricao,
                    'tipo' => $doc->tipo,
                    'tamanho_kb' => round($doc->tamanho / 1024, 2),
                    'publico' => $doc->publico ? 'Sim' : 'Não',
                    'sync_status' => $doc->sync_status,
                    'created_at' => $doc->created_at->format('d/m/Y H:i'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Título',
            'Descrição',
            'Tipo',
            'Tamanho (KB)',
            'Público',
            'Status Sincronização',
            'Data Criação',
        ];
    }
}
