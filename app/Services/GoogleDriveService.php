<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\DriveUploadResult;
use App\Models\Documento;
use App\Models\DriveAccount;
use App\Models\Foto;
use App\Models\Igreja;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

final class GoogleDriveService
{
    public function oauthIsConfigured(?DriveAccount $driveAccount = null): bool
    {
        $credentials = $this->resolveOauthClientCredentials($driveAccount);

        return $credentials['client_id'] !== null
            && $credentials['client_secret'] !== null
            && $credentials['redirect_uri'] !== null;
    }

    public function authorizationUrl(string $state, ?DriveAccount $driveAccount = null): string
    {
        $credentials = $this->resolveOauthClientCredentials($driveAccount);

        if (
            $credentials['client_id'] === null
            || $credentials['client_secret'] === null
            || $credentials['redirect_uri'] === null
        ) {
            throw new RuntimeException('Cliente OAuth do Google Drive nao configurado.');
        }

        $query = http_build_query([
            'client_id' => $credentials['client_id'],
            'redirect_uri' => $credentials['redirect_uri'],
            'response_type' => 'code',
            'scope' => $this->oauthScope(),
            'access_type' => 'offline',
            'prompt' => 'consent select_account',
            'include_granted_scopes' => 'true',
            'state' => $state,
        ]);

        return 'https://accounts.google.com/o/oauth2/v2/auth?'.$query;
    }

    public function upload(
        Documento $documento,
        ?DriveAccount $driveAccount = null,
        ?string $folderIdOverride = null,
    ): DriveUploadResult
    {
        $credentials = $this->resolveCredentials($driveAccount);
        $token = $this->requestAccessToken($credentials);
        $hasFolderOverride = $this->nullableString($folderIdOverride) !== null;

        $fileName = Str::slug(pathinfo($documento->titulo, PATHINFO_FILENAME) ?: $documento->titulo);
        $extension = pathinfo($documento->path, PATHINFO_EXTENSION);
        $name = $fileName !== '' ? $fileName : 'documento-'.$documento->id;
        $name = $extension !== '' ? $name.'.'.$extension : $name;
        $contents = $this->resolveDocumentoContents($documento);

        $folderId = $this->resolveDestinationFolder(
            driveAccount: $driveAccount,
            token: $token,
            folderIdOverride: $folderIdOverride,
            fallbackFolderId: $credentials['folder_id'],
            igreja: $documento->igreja,
            category: 'documentos',
        );

        $response = $this->uploadMultipartFile(
            token: $token,
            name: $name,
            mimeType: $documento->mime_type,
            contents: $contents,
            folderId: $folderId,
        );

        if (
            $response->status() === 404
            && ! $hasFolderOverride
            && $driveAccount instanceof DriveAccount
        ) {
            $folderId = $this->ensureChurchAssetFolder(
                driveAccount: $driveAccount,
                igreja: $documento->igreja,
                category: 'documentos',
                token: $token,
                forceRebuild: true,
            );

            $response = $this->uploadMultipartFile(
                token: $token,
                name: $name,
                mimeType: $documento->mime_type,
                contents: $contents,
                folderId: $folderId,
            );
        }

        if ($response->status() === 404 && $folderId !== null && $folderId !== '') {
            throw new RuntimeException($this->configuredFolderNotFoundMessage($folderId));
        }

        $this->ensureSuccess($response);

        /** @var array{id: string, webViewLink?: string|null} $payload */
        $payload = $response->json();

        if ($documento->publico) {
            $this->makePublic($payload['id'], $token);
        }

        return new DriveUploadResult(
            fileId: $payload['id'],
            webViewLink: $payload['webViewLink'] ?? $this->getLink($payload['id'], $driveAccount),
        );
    }

    public function uploadFoto(
        Foto $foto,
        ?DriveAccount $driveAccount = null,
        ?string $folderIdOverride = null,
    ): DriveUploadResult
    {
        $credentials = $this->resolveCredentials($driveAccount);
        $token = $this->requestAccessToken($credentials);
        $hasFolderOverride = $this->nullableString($folderIdOverride) !== null;

        $baseName = Str::slug(pathinfo($foto->nome_original, PATHINFO_FILENAME) ?: $foto->nome_original);
        $baseName = $baseName !== '' ? $baseName : 'foto-'.$foto->id;
        $extension = pathinfo($foto->caminho, PATHINFO_EXTENSION);

        if ($extension === '') {
            $extension = pathinfo($foto->nome_original, PATHINFO_EXTENSION);
        }

        $name = $extension !== '' ? $baseName.'.'.$extension : $baseName;
        $contents = $this->resolveFotoContents($foto);

        $folderId = $this->resolveDestinationFolder(
            driveAccount: $driveAccount,
            token: $token,
            folderIdOverride: $folderIdOverride,
            fallbackFolderId: $credentials['folder_id'],
            igreja: $foto->igreja,
            category: 'imagens',
        );

        $response = $this->uploadMultipartFile(
            token: $token,
            name: $name,
            mimeType: $foto->mime_type,
            contents: $contents,
            folderId: $folderId,
        );

        if (
            $response->status() === 404
            && ! $hasFolderOverride
            && $driveAccount instanceof DriveAccount
        ) {
            $folderId = $this->ensureChurchAssetFolder(
                driveAccount: $driveAccount,
                igreja: $foto->igreja,
                category: 'imagens',
                token: $token,
                forceRebuild: true,
            );

            $response = $this->uploadMultipartFile(
                token: $token,
                name: $name,
                mimeType: $foto->mime_type,
                contents: $contents,
                folderId: $folderId,
            );
        }

        if ($response->status() === 404 && $folderId !== null && $folderId !== '') {
            throw new RuntimeException($this->configuredFolderNotFoundMessage($folderId));
        }

        $this->ensureSuccess($response);

        /** @var array{id: string, webViewLink?: string|null} $payload */
        $payload = $response->json();

        if ($foto->is_public) {
            $this->makePublic($payload['id'], $token);
        }

        return new DriveUploadResult(
            fileId: $payload['id'],
            webViewLink: $payload['webViewLink'] ?? $this->getLink($payload['id'], $driveAccount),
        );
    }

    public function delete(string $fileId, ?DriveAccount $driveAccount = null): void
    {
        $credentials = $this->resolveCredentials($driveAccount);
        $token = $this->requestAccessToken($credentials);
        $response = Http::withToken($token)->delete('https://www.googleapis.com/drive/v3/files/'.$fileId, [
            'supportsAllDrives' => 'true',
        ]);

        if ($response->status() === 404) {
            return;
        }

        $this->ensureSuccess($response);
    }

    public function getLink(string $fileId, ?DriveAccount $driveAccount = null): ?string
    {
        $credentials = $this->resolveCredentials($driveAccount);
        $token = $this->requestAccessToken($credentials);
        $response = Http::withToken($token)->get('https://www.googleapis.com/drive/v3/files/'.$fileId, [
            'fields' => 'webViewLink,webContentLink',
            'supportsAllDrives' => 'true',
        ]);

        $this->ensureSuccess($response);

        $link = $response->json('webViewLink') ?? $response->json('webContentLink');

        return is_string($link) ? $link : null;
    }

    /**
     * @return array{
     *     email: ?string,
     *     display_name: ?string,
     *     root_folder_id: ?string,
     *     configured_folder: array{id: string, name: ?string, web_view_link: ?string}|null
     * }
     */
    public function testConnection(?DriveAccount $driveAccount = null): array
    {
        $credentials = $this->resolveCredentials($driveAccount);
        $token = $this->requestAccessToken($credentials);

        $response = Http::withToken($token)->get('https://www.googleapis.com/drive/v3/about', [
            'fields' => 'user(displayName,emailAddress)',
        ]);

        $this->ensureSuccess($response);

        $configuredFolder = null;

        if ($credentials['folder_id'] !== null && $credentials['folder_id'] !== '') {
            $folderResponse = Http::withToken($token)->get(
                'https://www.googleapis.com/drive/v3/files/'.$credentials['folder_id'],
                [
                    'fields' => 'id,name,mimeType,webViewLink',
                    'supportsAllDrives' => 'true',
                ],
            );

            if ($folderResponse->status() === 404) {
                throw new RuntimeException($this->configuredFolderNotFoundMessage($credentials['folder_id']));
            }

            $this->ensureSuccess($folderResponse);

            if ($folderResponse->json('mimeType') !== 'application/vnd.google-apps.folder') {
                throw new RuntimeException('O folder_id configurado nao aponta para uma pasta valida do Google Drive.');
            }

            $folderId = $this->nullableString($folderResponse->json('id'));

            if ($folderId === null) {
                throw new RuntimeException('O Google Drive nao retornou o identificador da pasta configurada.');
            }

            $configuredFolder = [
                'id' => $folderId,
                'name' => $this->nullableString($folderResponse->json('name')),
                'web_view_link' => $this->nullableString($folderResponse->json('webViewLink')),
            ];
        }

        return [
            'email' => $this->nullableString($response->json('user.emailAddress')),
            'display_name' => $this->nullableString($response->json('user.displayName')),
            'root_folder_id' => $this->fetchRootFolderId($token),
            'configured_folder' => $configuredFolder,
        ];
    }

    /**
     * @return array{
     *     refresh_token: ?string,
     *     email: ?string,
     *     display_name: ?string,
     *     root_folder_id: ?string
     * }
     */
    public function exchangeAuthorizationCode(string $code, ?DriveAccount $driveAccount = null): array
    {
        $credentials = $this->resolveOauthClientCredentials($driveAccount);

        if (
            $credentials['client_id'] === null
            || $credentials['client_secret'] === null
            || $credentials['redirect_uri'] === null
        ) {
            throw new RuntimeException('Cliente OAuth do Google Drive nao configurado.');
        }

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'code' => $code,
            'client_id' => $credentials['client_id'],
            'client_secret' => $credentials['client_secret'],
            'redirect_uri' => $credentials['redirect_uri'],
            'grant_type' => 'authorization_code',
        ]);

        $this->ensureSuccess($response);

        $accessToken = $this->nullableString($response->json('access_token'));

        if ($accessToken === null) {
            throw new RuntimeException('O Google nao retornou access token ao concluir a autorizacao.');
        }

        $aboutResponse = Http::withToken($accessToken)->get('https://www.googleapis.com/drive/v3/about', [
            'fields' => 'user(displayName,emailAddress)',
        ]);

        $this->ensureSuccess($aboutResponse);

        return [
            'refresh_token' => $this->nullableString($response->json('refresh_token')),
            'email' => $this->nullableString($aboutResponse->json('user.emailAddress')),
            'display_name' => $this->nullableString($aboutResponse->json('user.displayName')),
            'root_folder_id' => $this->fetchRootFolderId($accessToken),
        ];
    }

    /**
     * @param array{client_id: ?string, client_secret: ?string, refresh_token: ?string, folder_id: ?string} $credentials
     */
    private function requestAccessToken(array $credentials): string
    {
        if (empty($credentials['client_id']) || empty($credentials['client_secret'])) {
            throw new RuntimeException('Cliente OAuth do Google Drive nao configurado.');
        }

        if (empty($credentials['refresh_token'])) {
            throw new RuntimeException('Google Drive nao configurado. Conecte a conta via OAuth para gerar um refresh token.');
        }

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id' => $credentials['client_id'],
            'client_secret' => $credentials['client_secret'],
            'refresh_token' => $credentials['refresh_token'],
            'grant_type' => 'refresh_token',
        ]);

        $this->ensureSuccess($response);

        $token = $response->json('access_token');

        if (! is_string($token) || $token === '') {
            throw new RuntimeException('Falha ao obter access token do Google Drive.');
        }

        return $token;
    }

    private function makePublic(string $fileId, string $token): void
    {
        $response = Http::withToken($token)->post(
            'https://www.googleapis.com/drive/v3/files/'.$fileId.'/permissions?supportsAllDrives=true',
            [
                'role' => 'reader',
                'type' => 'anyone',
            ],
        );

        $this->ensureSuccess($response);
    }

    /**
     * @param array<string, mixed> $metadata
     */
    private function buildMultipartBody(string $boundary, array $metadata, string $mimeType, string $contents): string
    {
        $metadataJson = json_encode($metadata, JSON_THROW_ON_ERROR);

        return implode("\r\n", [
            '--'.$boundary,
            'Content-Type: application/json; charset=UTF-8',
            '',
            $metadataJson,
            '--'.$boundary,
            'Content-Type: '.$mimeType,
            '',
            $contents,
            '--'.$boundary.'--',
            '',
        ]);
    }

    private function uploadMultipartFile(
        string $token,
        string $name,
        string $mimeType,
        string $contents,
        ?string $folderId,
    ): Response {
        $metadata = ['name' => $name];

        if ($folderId !== null && $folderId !== '') {
            $metadata['parents'] = [$folderId];
        }

        $boundary = 'drive-boundary-'.Str::random(24);
        $body = $this->buildMultipartBody(
            boundary: $boundary,
            metadata: $metadata,
            mimeType: $mimeType,
            contents: $contents,
        );

        return $this->sendUploadRequest($token, $boundary, $body);
    }

    private function resolveDestinationFolder(
        ?DriveAccount $driveAccount,
        string $token,
        ?string $folderIdOverride,
        ?string $fallbackFolderId,
        ?Igreja $igreja,
        string $category,
    ): ?string {
        $folderId = $this->nullableString($folderIdOverride);

        if ($folderId !== null) {
            if (str_starts_with($folderId, '0A')) {
                throw new RuntimeException($this->configuredFolderNotFoundMessage($folderId));
            }

            return $folderId;
        }

        if ($driveAccount instanceof DriveAccount) {
            if ($igreja instanceof Igreja) {
                return $this->ensureChurchAssetFolder($driveAccount, $igreja, $category, $token);
            }

            return $this->ensureDefaultFolder($driveAccount, $token);
        }

        $fallback = $this->nullableString($fallbackFolderId);

        if ($fallback !== null && str_starts_with($fallback, '0A')) {
            throw new RuntimeException($this->configuredFolderNotFoundMessage($fallback));
        }

        return $fallback;
    }

    private function ensureChurchAssetFolder(
        DriveAccount $driveAccount,
        Igreja $igreja,
        string $category,
        string $token,
        bool $forceRebuild = false,
    ): string {
        $categoryKey = $category === 'imagens' ? 'imagens' : 'documentos';
        $metadata = Arr::wrap($driveAccount->metadata);
        $churchFolders = Arr::wrap($metadata['church_drive_folders'] ?? []);
        $churchKey = (string) $igreja->id;
        $churchData = Arr::wrap($churchFolders[$churchKey] ?? []);
        $documentosFolderId = $this->nullableString($churchData['documentos_folder_id'] ?? null);
        $imagensFolderId = $this->nullableString($churchData['imagens_folder_id'] ?? null);
        $cachedCategoryFolderId = $categoryKey === 'imagens' ? $imagensFolderId : $documentosFolderId;
        $hasBothCategoryFolders = $documentosFolderId !== null && $imagensFolderId !== null;

        if (! $forceRebuild && $cachedCategoryFolderId !== null && $hasBothCategoryFolders) {
            return $cachedCategoryFolderId;
        }

        $rootFolderId = $this->ensureDefaultFolder($driveAccount, $token, $forceRebuild);
        $churchFolderName = $this->resolveChurchFolderName($igreja);
        $churchFolderId = $this->nullableString($churchData['folder_id'] ?? null);

        if ($churchFolderId === null || $forceRebuild) {
            $churchFolderId = $this->createFolder($churchFolderName, $token, $rootFolderId);
        }

        if ($documentosFolderId === null || $forceRebuild) {
            $documentosFolderId = $this->createFolder('Documentos', $token, $churchFolderId);
        }

        if ($imagensFolderId === null || $forceRebuild) {
            $imagensFolderId = $this->createFolder('Imagens', $token, $churchFolderId);
        }

        $latestMetadata = Arr::wrap($driveAccount->fresh()->metadata);
        $latestChurchFolders = Arr::wrap($latestMetadata['church_drive_folders'] ?? []);
        $latestChurchData = Arr::wrap($latestChurchFolders[$churchKey] ?? []);

        $latestChurchFolders[$churchKey] = [
            ...$latestChurchData,
            ...$churchData,
            'igreja_id' => $igreja->id,
            'igreja_nome' => $churchFolderName,
            'folder_id' => $churchFolderId,
            'documentos_folder_id' => $documentosFolderId,
            'imagens_folder_id' => $imagensFolderId,
            'updated_at' => now()->toIso8601String(),
        ];
        $latestMetadata['church_drive_folders'] = $latestChurchFolders;

        $driveAccount->update([
            'metadata' => $latestMetadata,
        ]);

        $resolvedCategoryFolderId = $categoryKey === 'imagens'
            ? $imagensFolderId
            : $documentosFolderId;

        if ($resolvedCategoryFolderId === null) {
            throw new RuntimeException('Google Drive nao retornou o identificador da pasta de destino da igreja.');
        }

        return $resolvedCategoryFolderId;
    }

    private function resolveChurchFolderName(Igreja $igreja): string
    {
        $name = trim($igreja->nome_fantasia);

        if ($name !== '') {
            return $name;
        }

        return 'Igreja '.$igreja->id;
    }

    private function ensureSuccess(Response $response): void
    {
        if ($response->successful()) {
            return;
        }

        throw new RuntimeException('Google Drive respondeu com erro: '.$response->body());
    }

    private function sendUploadRequest(string $token, string $boundary, string $body): Response
    {
        return Http::withToken($token)->send(
            'POST',
            'https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&fields=id,webViewLink&supportsAllDrives=true',
            [
                'headers' => [
                    'Content-Type' => 'multipart/related; boundary='.$boundary,
                ],
                'body' => $body,
            ],
        );
    }

    /**
     * @return array{client_id: ?string, client_secret: ?string, refresh_token: ?string, folder_id: ?string}
     */
    private function resolveCredentials(?DriveAccount $driveAccount): array
    {
        return [
            'client_id' => $driveAccount instanceof DriveAccount
                ? $this->nullableString($driveAccount->client_id) ?? $this->configString('services.google_drive.client_id')
                : $this->configString('services.google_drive.client_id'),
            'client_secret' => $driveAccount instanceof DriveAccount
                ? $this->nullableString($driveAccount->client_secret) ?? $this->configString('services.google_drive.client_secret')
                : $this->configString('services.google_drive.client_secret'),
            'refresh_token' => $driveAccount instanceof DriveAccount
                ? $this->nullableString($driveAccount->refresh_token)
                : $this->configString('services.google_drive.refresh_token'),
            'folder_id' => $driveAccount instanceof DriveAccount
                ? $this->nullableString($driveAccount->folder_id)
                : $this->configString('services.google_drive.folder_id'),
        ];
    }

    private function configString(string $key): ?string
    {
        $value = config($key);

        return is_string($value) && $value !== '' ? $value : null;
    }

    /**
     * @return array{client_id: ?string, client_secret: ?string, redirect_uri: ?string}
     */
    private function resolveOauthClientCredentials(?DriveAccount $driveAccount): array
    {
        $clientId = $driveAccount instanceof DriveAccount
            ? $this->nullableString($driveAccount->client_id) ?? $this->configString('services.google_drive.client_id')
            : $this->configString('services.google_drive.client_id');
        $clientSecret = $driveAccount instanceof DriveAccount
            ? $this->nullableString($driveAccount->client_secret) ?? $this->configString('services.google_drive.client_secret')
            : $this->configString('services.google_drive.client_secret');

        return [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $this->configString('services.google_drive.redirect_uri'),
        ];
    }

    private function oauthScope(): string
    {
        return $this->configString('services.google_drive.oauth_scope')
            ?? 'https://www.googleapis.com/auth/drive.file';
    }

    private function configuredFolderNotFoundMessage(string $folderId): string
    {
        $message = 'O folder_id configurado "'.$folderId.'" nao foi encontrado ou a conta autenticada nao tem acesso a ele.';

        if (str_starts_with($folderId, '0A')) {
            $message .= ' Esse identificador parece ser de um Shared Drive, nao de uma pasta interna.';
        }

        if (str_contains($this->oauthScope(), 'drive.file')) {
            $message .= ' Com o escopo drive.file, o Google so libera arquivos e pastas que o app criou ou que o usuario escolheu explicitamente por um Picker.';
        }

        return $message;
    }

    /**
     * Cria uma pasta no Google Drive e retorna o ID.
     */
    public function createFolder(string $name, string $token, ?string $parentId = null): string
    {
        $metadata = [
            'name' => $name,
            'mimeType' => 'application/vnd.google-apps.folder',
        ];

        if ($parentId !== null && $parentId !== '') {
            $metadata['parents'] = [$parentId];
        }

        $response = Http::withToken($token)
            ->post('https://www.googleapis.com/drive/v3/files?fields=id&supportsAllDrives=true', $metadata);

        $this->ensureSuccess($response);

        $id = $response->json('id');

        if (! is_string($id) || $id === '') {
            throw new RuntimeException('Google Drive nao retornou o ID da pasta criada.');
        }

        return $id;
    }

    /**
     * Garante que a DriveAccount tenha um folder_id configurado.
     * Se nao tiver, cria a pasta "Acervo da Igreja" na raiz e salva o ID.
     */
    public function ensureDefaultFolder(
        DriveAccount $driveAccount,
        ?string $token = null,
        bool $forceRecreate = false,
    ): string
    {
        $folderId = $this->nullableString($driveAccount->folder_id);

        if ($folderId !== null && str_starts_with($folderId, '0A')) {
            $forceRecreate = true;
        }

        if ($folderId !== null && ! $forceRecreate) {
            return $folderId;
        }

        if ($token === null || $token === '') {
            $credentials = $this->resolveCredentials($driveAccount);
            $token = $this->requestAccessToken($credentials);
        }

        $newFolderId = $this->createFolder('Acervo da Igreja', $token);
        $metadata = Arr::wrap($driveAccount->metadata);

        if ($forceRecreate && $folderId !== null) {
            $metadata['replaced_folder_id'] = $folderId;
            $metadata['folder_replaced_at'] = now()->toIso8601String();
        }

        $driveAccount->update([
            'folder_id' => $newFolderId,
            'metadata' => $metadata !== [] ? $metadata : null,
        ]);

        return $newFolderId;
    }

    private function fetchRootFolderId(string $token): ?string
    {
        $response = Http::withToken($token)->get('https://www.googleapis.com/drive/v3/files/root', [
            'fields' => 'id',
            'supportsAllDrives' => 'true',
        ]);

        if ($response->status() === 404) {
            return null;
        }

        if (! $response->successful()) {
            Log::warning('Falha ao consultar pasta root no Google Drive.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        }

        return $this->nullableString($response->json('id'));
    }

    private function resolveDocumentoContents(Documento $documento): string
    {
        $contents = Storage::disk($documento->disk)->get($documento->path);

        if (! is_string($contents)) {
            throw new RuntimeException('Nao foi possivel ler o arquivo local para sincronizacao.');
        }

        return $contents;
    }

    private function resolveFotoContents(Foto $foto): string
    {
        $contents = Storage::disk($foto->disk)->get($foto->caminho);

        if (! is_string($contents)) {
            throw new RuntimeException('Nao foi possivel ler a foto local para sincronizacao.');
        }

        return $contents;
    }

    private function nullableString(mixed $value): ?string
    {
        return is_string($value) && $value !== '' ? $value : null;
    }
}
