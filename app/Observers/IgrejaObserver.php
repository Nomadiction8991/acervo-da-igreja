<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Igreja;
use Illuminate\Support\Facades\Cache;

final class IgrejaObserver
{
    public function created(Igreja $igreja): void
    {
        $this->invalidateCache();
    }

    public function updated(Igreja $igreja): void
    {
        $this->invalidateCache();
    }

    public function deleted(Igreja $igreja): void
    {
        $this->invalidateCache();
    }

    public function restored(Igreja $igreja): void
    {
        $this->invalidateCache();
    }

    private function invalidateCache(): void
    {
        Cache::forget('dashboard_total_igrejas');
        Cache::forget('dashboard_total_cidades');
    }
}
