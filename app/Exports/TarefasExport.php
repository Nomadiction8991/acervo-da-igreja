<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Tarefa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

final class TarefasExport implements FromCollection, WithHeadings
{
    public function collection(): Collection
    {
        return Tarefa::query()
            ->with(['igreja', 'user'])
            ->select('titulo', 'descricao', 'status', 'prioridade', 'due_at', 'created_at')
            ->orderByDesc('created_at')
            ->get()
            ->map(function (Tarefa $tarefa) {
                return [
                    'titulo' => $tarefa->titulo,
                    'descricao' => $tarefa->descricao,
                    'status' => $tarefa->status->label(),
                    'prioridade' => $tarefa->prioridade->label(),
                    'prazo' => $tarefa->due_at?->format('d/m/Y H:i'),
                    'criada_em' => $tarefa->created_at->format('d/m/Y H:i'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Título',
            'Descrição',
            'Status',
            'Prioridade',
            'Prazo',
            'Data Criação',
        ];
    }
}
