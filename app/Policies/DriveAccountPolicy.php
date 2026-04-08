<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\DriveAccount;
use App\Models\User;

final class DriveAccountPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('drive_accounts.visualizar');
    }

    public function view(User $user, DriveAccount $driveAccount): bool
    {
        return $user->hasPermission('drive_accounts.visualizar');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('drive_accounts.criar');
    }

    public function update(User $user, DriveAccount $driveAccount): bool
    {
        return $user->hasPermission('drive_accounts.editar');
    }

    public function delete(User $user, DriveAccount $driveAccount): bool
    {
        return $user->hasPermission('drive_accounts.deletar');
    }

    public function testConnection(User $user, DriveAccount $driveAccount): bool
    {
        return $user->hasPermission('drive_accounts.testar');
    }
}
