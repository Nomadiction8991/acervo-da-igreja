<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\AuditLog;
use App\Models\Documento;
use App\Models\DriveAccount;
use App\Models\Foto;
use App\Models\GrupoDocumento;
use App\Models\Igreja;
use App\Models\Tarefa;
use App\Models\User;
use App\Observers\AuditLogObserver;
use App\Observers\DocumentoObserver;
use App\Observers\DriveAccountObserver;
use App\Observers\FotoObserver;
use App\Observers\GrupoDocumentoObserver;
use App\Observers\IgrejaObserver;
use App\Observers\TarefaObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Igreja::observe(IgrejaObserver::class);
        Foto::observe(FotoObserver::class);
        GrupoDocumento::observe(GrupoDocumentoObserver::class);
        Documento::observe(DocumentoObserver::class);
        DriveAccount::observe(DriveAccountObserver::class);
        Tarefa::observe(TarefaObserver::class);
        User::observe(UserObserver::class);
        AuditLog::observe(AuditLogObserver::class);
    }
}
