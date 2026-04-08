<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Igreja;
use App\Models\User;

final class IgrejaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('igrejas.visualizar');
    }

    public function view(User $user, Igreja $igreja): bool
    {
        return $user->hasPermission('igrejas.visualizar');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('igrejas.criar');
    }

    public function update(User $user, Igreja $igreja): bool
    {
        return $user->hasPermission('igrejas.editar');
    }

    public function delete(User $user, Igreja $igreja): bool
    {
        return $user->hasPermission('igrejas.deletar');
    }

    public function alterarVisibilidade(User $user, Igreja $igreja): bool
    {
        return $user->hasPermission('igrejas.alterar_visibilidade');
    }
}
