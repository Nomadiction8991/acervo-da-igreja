<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\Igreja;
use App\Models\Tarefa;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;

final class RelatórioController extends Controller
{
    use AuthorizesRequests;

    public function dashboard(): View
    {
        $this->authorize('viewAny', Igreja::class);

        // Igrejas por cidade
        $igrejasPorCidade = Igreja::query()
            ->whereNotNull('cidade')
            ->selectRaw('cidade, COUNT(*) as total')
            ->groupBy('cidade')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // Documentos por tipo
        $documentosPorTipo = Documento::query()
            ->selectRaw('tipo, COUNT(*) as total')
            ->groupBy('tipo')
            ->orderByDesc('total')
            ->get();

        // Tarefas por status
        $tarefasPorStatus = Tarefa::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get();

        // Tarefas por prioridade
        $tarefasPorPrioridade = Tarefa::query()
            ->selectRaw('prioridade, COUNT(*) as total')
            ->groupBy('prioridade')
            ->get();

        return view('relatorios.dashboard', compact(
            'igrejasPorCidade',
            'documentosPorTipo',
            'tarefasPorStatus',
            'tarefasPorPrioridade',
        ));
    }
}
