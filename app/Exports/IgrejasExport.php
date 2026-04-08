<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Igreja;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

final class IgrejasExport implements FromCollection, WithHeadings
{
    public function collection(): Collection
    {
        return Igreja::query()
            ->select('codigo_controle', 'nome_fantasia', 'razao_social', 'cidade', 'estado', 'created_at')
            ->orderBy('nome_fantasia')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Código Controle',
            'Nome Fantasia',
            'Razão Social',
            'Cidade',
            'Estado',
            'Data Criação',
        ];
    }
}
