<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

final class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('users.visualizar');
    }

    public function view(User $user, User $model): bool
    {
        return $user->hasPermission('users.visualizar');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('users.criar');
    }

    public function update(User $user, User $model): bool
    {
        if ($model->is_admin) {
            return false;
        }

        return $user->hasPermission('users.editar');
    }

    public function delete(User $user, User $model): bool
    {
        if ($model->is_admin) {
            return false;
        }

        return $user->hasPermission('users.deletar');
    }
}
