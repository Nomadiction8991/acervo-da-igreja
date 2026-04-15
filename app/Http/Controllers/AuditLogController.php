<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\AuditLog\FilterAuditLogRequest;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Collection;
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
            'modulos' => $this->filterOptionsFor('modulo'),
            'acoes' => $this->filterOptionsFor('acao'),
        ]);
    }

    /**
     * @return Collection<int, string>
     */
    private function filterOptionsFor(string $column): Collection
    {
        /** @var mixed $cachedValues */
        $cachedValues = cache()->remember(
            key: sprintf('audit_log_%s_options_v2', $column),
            ttl: 3600,
            callback: static fn (): array => AuditLog::query()
                ->select($column)
                ->distinct()
                ->orderBy($column)
                ->pluck($column)
                ->all(),
        );

        $values = match (true) {
            $cachedValues instanceof Collection => $cachedValues->all(),
            is_array($cachedValues) => $cachedValues,
            default => [],
        };

        return collect($values)
            ->map(static function (mixed $value): ?string {
                if (is_string($value)) {
                    return $value;
                }

                if (is_int($value) || is_float($value) || is_bool($value)) {
                    return (string) $value;
                }

                if (is_array($value)) {
                    $json = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                    return $json === false ? null : $json;
                }

                return null;
            })
            ->filter(static fn (?string $value): bool => filled($value))
            ->values();
    }
}
