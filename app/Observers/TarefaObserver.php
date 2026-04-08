<?php

declare(strict_types=1);

namespace App\Observers;

final class TarefaObserver extends BaseAuditableObserver
{
    protected function module(): string
    {
        return 'tarefas';
    }
}
