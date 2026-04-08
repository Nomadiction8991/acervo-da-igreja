<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Documento;
use App\Models\User;

final class DocumentoPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('documentos.visualizar');
    }

    public function view(?User $user, Documento $documento): bool
    {
        return $documento->publico || ($user?->hasPermission('documentos.visualizar') ?? false);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('documentos.criar');
    }

    public function update(User $user, Documento $documento): bool
    {
        return $user->hasPermission('documentos.editar');
    }

    public function delete(User $user, Documento $documento): bool
    {
        return $user->hasPermission('documentos.deletar');
    }
}
