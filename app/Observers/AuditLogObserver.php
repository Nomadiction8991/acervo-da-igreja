<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Cache;

final class AuditLogObserver
{
    public function created(AuditLog $log): void
    {
        Cache::forget('dashboard_total_logs');
    }
}
