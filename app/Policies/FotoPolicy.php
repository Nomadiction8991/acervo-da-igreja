<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Foto;
use App\Models\User;

final class FotoPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('fotos.visualizar');
    }

    public function view(?User $user, Foto $foto): bool
    {
        return $foto->isPublic() || ($user?->hasPermission('fotos.visualizar') ?? false);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('fotos.criar');
    }

    public function update(User $user, Foto $foto): bool
    {
        return $user->hasPermission('fotos.editar');
    }

    public function delete(User $user, Foto $foto): bool
    {
        return $user->hasPermission('fotos.deletar');
    }

    public function alterarVisibilidade(User $user, Foto $foto): bool
    {
        return $user->hasPermission('fotos.alterar_visibilidade');
    }
}
