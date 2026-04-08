<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;

final class AuditLogService
{
    /**
     * @param array<string, mixed>|null $oldValues
     * @param array<string, mixed>|null $newValues
     */
    public function logModelEvent(
        string $module,
        string $action,
        Model $model,
        ?array $oldValues = null,
        ?array $newValues = null,
    ): AuditLog {
        $key = $model->getKey();
        $entityId = is_numeric($key) ? (int) $key : 0;

        return $this->log(
            action: $action,
            module: $module,
            entity: $model::class,
            entityId: $entityId,
            oldValues: $oldValues,
            newValues: $newValues,
        );
    }

    /**
     * @param array<string, mixed>|null $oldValues
     * @param array<string, mixed>|null $newValues
     */
    public function log(
        string $action,
        string $module,
        string $entity,
        int $entityId,
        ?array $oldValues = null,
        ?array $newValues = null,
    ): AuditLog {
        $log = AuditLog::query()->create([
            'user_id' => Auth::id(),
            'acao' => $action,
            'modulo' => $module,
            'entidade' => $entity,
            'entidade_id' => $entityId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'antes' => $oldValues,
            'depois' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);

        // Invalidar cache dos filtros de auditoria
        Cache::forget('audit_log_modulos');
        Cache::forget('audit_log_acoes');

        return $log;
    }
}
