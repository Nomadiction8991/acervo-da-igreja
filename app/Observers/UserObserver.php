<?php

declare(strict_types=1);

namespace App\Observers;

final class UserObserver extends BaseAuditableObserver
{
    protected function module(): string
    {
        return 'users';
    }

    /**
     * @return list<string>
     */
    protected function ignoredKeys(): array
    {
        return [
            'password',
            'remember_token',
        ];
    }
}
