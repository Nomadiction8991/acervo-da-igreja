<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Foto;
use Illuminate\Support\Facades\Cache;

final class FotoObserver
{
    public function created(Foto $foto): void
    {
        $this->invalidateCache();
    }

    public function updated(Foto $foto): void
    {
        $this->invalidateCache();
    }

    public function deleted(Foto $foto): void
    {
        $this->invalidateCache();
    }

    private function invalidateCache(): void
    {
        Cache::forget('dashboard_total_fotos_publicas');
    }
}
