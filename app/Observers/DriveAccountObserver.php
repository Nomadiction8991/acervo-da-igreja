<?php

declare(strict_types=1);

namespace App\Observers;

final class DriveAccountObserver extends BaseAuditableObserver
{
    protected function module(): string
    {
        return 'drive_accounts';
    }

    /**
     * @return list<string>
     */
    protected function ignoredKeys(): array
    {
        return [
            'client_id',
            'client_secret',
            'refresh_token',
        ];
    }
}
