<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\AdminUserData;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

final class AdminUserService
{
    public function ensureDefaultAdmin(?AdminUserData $data = null): User
    {
        $admin = $data ?? AdminUserData::fromArray((array) config('admin.default', []));

        /** @var User $user */
        $user = User::query()->updateOrCreate(
            ['email' => $admin->email],
            [
                'name' => $admin->name,
                'password' => Hash::make($admin->password),
                'email_verified_at' => now(),
                'is_admin' => true,
                'is_active' => true,
            ],
        );

        return $user;
    }
}
