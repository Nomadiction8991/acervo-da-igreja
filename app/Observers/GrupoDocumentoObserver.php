<?php

declare(strict_types=1);

namespace App\Observers;

final class GrupoDocumentoObserver extends BaseAuditableObserver
{
    protected function module(): string
    {
        return 'grupos_documentos';
    }
}
