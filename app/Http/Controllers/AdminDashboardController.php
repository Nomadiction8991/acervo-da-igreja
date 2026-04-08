<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Igreja;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

final class AdminDashboardController extends Controller
{
    public function __invoke(): View
    {
        // Cache estatísticas por 5 minutos
        $totalIgrejas = Cache::remember('dashboard_total_igrejas', 300, static function (): int {
            return Igreja::count();
        });

        $totalCidades = Cache::remember('dashboard_total_cidades', 300, static function (): int {
            return Igreja::whereNotNull('cidade')->distinct('cidade')->count('cidade');
        });

        $totalFotosPublicas = Cache::remember('dashboard_total_fotos_publicas', 300, static function (): int {
            return \App\Models\Foto::where('is_public', true)->count();
        });

        $totalLogs = Cache::remember('dashboard_total_logs', 300, static function (): int {
            return AuditLog::count();
        });

        $igrejas = Igreja::withCount(['fotos', 'documentos', 'tarefas'])
            ->orderByDesc('updated_at')
            ->limit(6)
            ->get();

        $logs = AuditLog::with('user')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalIgrejas',
            'totalCidades',
            'totalFotosPublicas',
            'totalLogs',
            'igrejas',
            'logs',
        ));
    }
}
