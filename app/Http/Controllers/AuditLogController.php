<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\AuditLog\FilterAuditLogRequest;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\View\View;

final class AuditLogController extends Controller
{
    public function index(FilterAuditLogRequest $request): View
    {
        $logs = AuditLog::query()
            ->with('user')
            ->when($request->filled('user_id'), static function ($query) use ($request): void {
                $query->where('user_id', $request->integer('user_id'));
            })
            ->when($request->filled('acao'), static function ($query) use ($request): void {
                $query->where('acao', $request->string('acao')->toString());
            })
            ->when($request->filled('modulo'), static function ($query) use ($request): void {
                $query->where('modulo', $request->string('modulo')->toString());
            })
            ->when($request->filled('data_inicial'), static function ($query) use ($request): void {
                $query->whereDate('created_at', '>=', $request->date('data_inicial'));
            })
            ->when($request->filled('data_final'), static function ($query) use ($request): void {
                $query->whereDate('created_at', '<=', $request->date('data_final'));
            })
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('audit-logs.index', [
            'logs' => $logs,
            'users' => User::query()->orderBy('name')->get(),
            'modulos' => cache()->remember('audit_log_modulos', 3600, static function (): \Illuminate\Support\Collection {
                return AuditLog::query()->select('modulo')->distinct()->orderBy('modulo')->pluck('modulo');
            }),
            'acoes' => cache()->remember('audit_log_acoes', 3600, static function (): \Illuminate\Support\Collection {
                return AuditLog::query()->select('acao')->distinct()->orderBy('acao')->pluck('acao');
            }),
        ]);
    }
}
