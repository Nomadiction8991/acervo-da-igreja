<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\DriveAccount;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

final readonly class DriveAccountService
{
    public function __construct(
        private GoogleDriveService $googleDriveService,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function store(array $data): DriveAccount
    {
        /** @var DriveAccount */
        return DB::transaction(function () use ($data): DriveAccount {
            $payload = $this->normalizeData($data);

            /** @var DriveAccount $driveAccount */
            $driveAccount = DriveAccount::query()->create($payload);

            return $driveAccount->refresh();
        });
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(DriveAccount $driveAccount, array $data): DriveAccount
    {
        /** @var DriveAccount */
        return DB::transaction(function () use ($driveAccount, $data): DriveAccount {
            $payload = $this->normalizeData($data, $driveAccount);

            $driveAccount->update($payload);

            return $driveAccount->refresh();
        });
    }

    public function delete(DriveAccount $driveAccount): void
    {
        DB::transaction(static function () use ($driveAccount): void {
            $driveAccount->delete();
        });
    }

    public function oauthIsConfigured(?DriveAccount $driveAccount = null): bool
    {
        return $this->googleDriveService->oauthIsConfigured($driveAccount);
    }

    public function authorizationUrl(string $state, DriveAccount $driveAccount): string
    {
        return $this->googleDriveService->authorizationUrl($state, $driveAccount);
    }

    /**
     * @return array{
     *     email: ?string,
     *     display_name: ?string,
     *     root_folder_id: ?string,
     *     configured_folder: array{id: string, name: ?string, web_view_link: ?string}|null
     * }
     */
    public function testConnection(DriveAccount $driveAccount): array
    {
        $result = $this->googleDriveService->testConnection($driveAccount);

        DB::transaction(function () use ($driveAccount, $result): void {
            $metadata = Arr::wrap($driveAccount->metadata);
            $metadata['last_tested_at'] = now()->toIso8601String();
            $metadata['display_name'] = $result['display_name'];
            $metadata['root_folder_id'] = $result['root_folder_id'];
            $metadata['configured_folder'] = $result['configured_folder'];

            $driveAccount->update([
                'email' => $result['email'] ?? $driveAccount->email,
                'metadata' => $metadata,
            ]);
        });

        return $result;
    }

    /**
     * @return array{email: ?string, display_name: ?string, root_folder_id: ?string, refresh_token: ?string}
     */
    public function connectWithAuthorizationCode(DriveAccount $driveAccount, string $code): array
    {
        $result = $this->googleDriveService->exchangeAuthorizationCode($code, $driveAccount);
        $refreshToken = $result['refresh_token'] ?? $driveAccount->refresh_token;

        if (! is_string($refreshToken) || trim($refreshToken) === '') {
            throw new RuntimeException('O Google nao retornou refresh token. Revogue o acesso anterior e tente novamente com consentimento.');
        }

        DB::transaction(function () use ($driveAccount, $result, $refreshToken): void {
            $metadata = Arr::wrap($driveAccount->metadata);
            $metadata['oauth_connected_at'] = now()->toIso8601String();
            $metadata['connection_method'] = 'oauth';
            $metadata['display_name'] = $result['display_name'];
            $metadata['root_folder_id'] = $result['root_folder_id'];

            $driveAccount->update([
                'refresh_token' => $refreshToken,
                'email' => $result['email'] ?? $driveAccount->email,
                'metadata' => $metadata,
            ]);
        });

        $driveAccount->refresh();

        try {
            $this->googleDriveService->ensureDefaultFolder($driveAccount);
        } catch (Throwable $throwable) {
            Log::warning('Falha ao inicializar pasta base do Google Drive apos OAuth.', [
                'drive_account_id' => $driveAccount->id,
                'message' => $throwable->getMessage(),
            ]);
        }

        return [
            'email' => $result['email'],
            'display_name' => $result['display_name'],
            'root_folder_id' => $result['root_folder_id'],
            'refresh_token' => $refreshToken,
        ];
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function normalizeData(array $data, ?DriveAccount $driveAccount = null): array
    {
        $data['provider'] = 'google_drive';

        foreach (['client_id', 'client_secret', 'refresh_token'] as $field) {
            $value = $data[$field] ?? null;

            if (! is_string($value) || trim($value) === '') {
                unset($data[$field]);

                if ($driveAccount !== null) {
                    $data[$field] = $driveAccount->getAttribute($field);
                }
            }
        }

        if (isset($data['folder_id']) && is_string($data['folder_id'])) {
            $data['folder_id'] = trim($data['folder_id']) !== '' ? trim($data['folder_id']) : null;
        }

        if (isset($data['email']) && is_string($data['email'])) {
            $data['email'] = trim($data['email']) !== '' ? trim($data['email']) : null;
        }

        $hasManualCredentials = ! empty($data['client_id'])
            && ! empty($data['client_secret'])
            && ! empty($data['refresh_token']);

        $metadata = Arr::wrap($driveAccount?->metadata);

        if ($hasManualCredentials) {
            $metadata['connection_method'] = 'manual';
        }

        if ($metadata !== []) {
            $data['metadata'] = $metadata;
        }

        return $data;
    }
}
