<?php

declare(strict_types=1);

namespace App\Services;

use App\Jobs\SyncFotoToDriveJob;
use App\Models\DriveAccount;
use App\Models\Foto;
use App\Models\Igreja;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

final class FotoService
{
    /**
     * @param array<int, UploadedFile> $files
     * @return array<int, Foto>
     */
    public function storeMany(Igreja $igreja, array $files, bool $isPublic): array
    {
        $storedPaths = [];

        try {
            /** @var array<int, Foto> */
            return DB::transaction(function () use ($igreja, $files, $isPublic, &$storedPaths): array {
                $maxOrder = $igreja->fotos()->max('ordem');
                $ordem = is_numeric($maxOrder) ? (int) $maxOrder : 0;
                $hasPrincipal = $igreja->fotos()->where('is_principal', true)->exists();
                $fotos = [];

                foreach ($files as $file) {
                    $path = $file->store('uploads/fotos', 'local');
                    if (! is_string($path) || $path === '') {
                        throw new \RuntimeException('Nao foi possivel salvar a foto enviada.');
                    }

                    $storedPaths[] = $path;

                    $fotos[] = $igreja->fotos()->create([
                        'drive_account_id' => null,
                        'drive_folder_id' => null,
                        'caminho' => $path,
                        'disk' => 'local',
                        'nome_original' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType() ?? 'application/octet-stream',
                        'tamanho' => $file->getSize(),
                        'is_public' => $isPublic,
                        'is_principal' => ! $hasPrincipal && $ordem === 0,
                        'ordem' => ++$ordem,
                        'drive_file_id' => null,
                        'drive_link' => null,
                        'sync_status' => null,
                        'sync_error' => null,
                        'synced_at' => null,
                    ]);

                    $hasPrincipal = true;
                }

                return $fotos;
            });
        } catch (Throwable $throwable) {
            foreach ($storedPaths as $path) {
                Storage::disk('local')->delete($path);
            }

            throw $throwable;
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(Foto $foto, array $data): Foto
    {
        /** @var Foto */
        return DB::transaction(function () use ($foto, $data): Foto {
            if ((bool) ($data['is_principal'] ?? false)) {
                $foto->igreja->fotos()->update(['is_principal' => false]);
            }

            $foto->update($data);

            return $foto->refresh();
        });
    }

    public function triggerManualSync(Foto $foto): void
    {
        DB::transaction(function () use ($foto): void {
            $driveAccount = $foto->driveAccount;

            if (! $driveAccount instanceof DriveAccount) {
                $driveAccount = $this->resolveDefaultDriveAccount();
            }

            if (! $driveAccount instanceof DriveAccount) {
                throw new RuntimeException('Nenhuma conta Google Drive conectada foi encontrada para sincronizar a foto.');
            }

            if (! is_string($driveAccount->refresh_token) || trim($driveAccount->refresh_token) === '') {
                throw new RuntimeException('A conta Google Drive selecionada ainda nao esta conectada. Conecte via OAuth e tente novamente.');
            }

            $foto->update([
                'drive_account_id' => $driveAccount->id,
                'sync_status' => 'pending',
                'sync_error' => null,
                'drive_file_id' => null,
                'drive_link' => null,
                'synced_at' => null,
            ]);

            DB::afterCommit(static function () use ($foto): void {
                SyncFotoToDriveJob::dispatch($foto->id);
            });
        });
    }

    public function delete(Foto $foto): void
    {
        $disk = $foto->disk;
        $path = $foto->caminho;
        $igreja = $foto->igreja;
        $wasPrincipal = $foto->is_principal;

        DB::transaction(static function () use ($foto, $disk, $path, $igreja, $wasPrincipal): void {
            $foto->delete();

            if ($wasPrincipal) {
                /** @var Foto|null $nextPrincipal */
                $nextPrincipal = $igreja->fotos()
                    ->orderByDesc('is_public')
                    ->orderBy('ordem')
                    ->first();

                if ($nextPrincipal !== null) {
                    $nextPrincipal->update(['is_principal' => true]);
                }
            }

            DB::afterCommit(static function () use ($disk, $path): void {
                Storage::disk($disk)->delete($path);
            });
        });
    }

    private function resolveDefaultDriveAccount(): ?DriveAccount
    {
        /** @var DriveAccount|null $activeAccount */
        $activeAccount = DriveAccount::query()
            ->where('is_active', true)
            ->whereNotNull('refresh_token')
            ->orderBy('id')
            ->first();

        if ($activeAccount instanceof DriveAccount) {
            return $activeAccount;
        }

        /** @var DriveAccount|null */
        return DriveAccount::query()
            ->whereNotNull('refresh_token')
            ->orderBy('id')
            ->first();
    }
}
