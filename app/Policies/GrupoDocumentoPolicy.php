<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\GrupoDocumento;
use App\Models\User;

final class GrupoDocumentoPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('grupos_documentos.visualizar');
    }

    public function view(User $user, GrupoDocumento $grupoDocumento): bool
    {
        return $user->hasPermission('grupos_documentos.visualizar');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('grupos_documentos.criar');
    }

    public function update(User $user, GrupoDocumento $grupoDocumento): bool
    {
        return $user->hasPermission('grupos_documentos.editar');
    }

    public function delete(User $user, GrupoDocumento $grupoDocumento): bool
    {
        return $user->hasPermission('grupos_documentos.deletar');
    }
}
