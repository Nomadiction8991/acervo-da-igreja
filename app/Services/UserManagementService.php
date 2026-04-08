<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use DomainException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final class UserManagementService
{
    /**
     * @param array<string, mixed> $data
     */
    public function store(array $data): User
    {
        /** @var list<int> $permissionIds */
        $permissionIds = array_map(
            static fn (mixed $value): int => is_numeric($value) ? (int) $value : 0,
            Arr::wrap(Arr::pull($data, 'permission_ids', [])),
        );
        $data['password'] = Hash::make((string) $data['password']);

        /** @var User */
        return DB::transaction(function () use ($data, $permissionIds): User {
            /** @var User $user */
            $user = User::query()->create($data);
            $user->permissions()->sync($permissionIds);

            return $user->refresh();
        });
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(User $user, array $data): User
    {
        /** @var list<int> $permissionIds */
        $permissionIds = array_map(
            static fn (mixed $value): int => is_numeric($value) ? (int) $value : 0,
            Arr::wrap(Arr::pull($data, 'permission_ids', [])),
        );

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make((string) $data['password']);
        }

        /** @var User */
        return DB::transaction(function () use ($user, $data, $permissionIds): User {
            $user->update($data);
            $user->permissions()->sync($permissionIds);

            return $user->refresh();
        });
    }

    public function delete(User $user, User $actingUser): void
    {
        if ($user->is($actingUser)) {
            throw new DomainException('O usuario autenticado nao pode remover a propria conta administrativa.');
        }

        DB::transaction(static function () use ($user): void {
            $user->permissions()->detach();
            $user->delete();
        });
    }
}
