<?php

declare(strict_types=1);

namespace App\Observers;

final class DocumentoObserver extends BaseAuditableObserver
{
    protected function module(): string
    {
        return 'documentos';
    }
}
