<?php

declare(strict_types=1);

namespace App\Services;

use App\Jobs\SyncFileToDriveJob;
use App\Models\Documento;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

final class DocumentoService
{
    /**
     * @param array<string, mixed> $data
     */
    public function store(array $data, UploadedFile $file, User $user): Documento
    {
        $path = null;
        $data = $this->normalizeDriveTarget($data);

        try {
            /** @var Documento */
            return DB::transaction(function () use ($data, $file, $user, &$path): Documento {
                $path = $file->store('uploads', 'local');
                /** @var string $type */
                $type = $data['tipo'];

                /** @var Documento $documento */
                $documento = Documento::query()->create([
                    ...$data,
                    'user_id' => $user->id,
                    'path' => $path,
                    'disk' => 'local',
                    'tipo' => $type,
                    'mime_type' => $file->getMimeType() ?? 'application/octet-stream',
                    'tamanho' => $file->getSize(),
                    'sync_status' => null,
                    'sync_error' => null,
                    'drive_file_id' => null,
                    'drive_link' => null,
                    'synced_at' => null,
                ]);

                return $documento;
            });
        } catch (Throwable $throwable) {
            if (is_string($path) && $path !== '') {
                Storage::disk('local')->delete($path);
            }

            throw $throwable;
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(Documento $documento, array $data, ?UploadedFile $file = null): Documento
    {
        $newPath = null;
        $oldPath = null;
        $oldDisk = $documento->disk;
        $data = $this->normalizeDriveTarget($data);
        $driveTargetChanged = $this->driveTargetChanged($documento, $data);

        try {
            /** @var Documento */
            return DB::transaction(function () use (
                $documento,
                $data,
                $file,
                &$newPath,
                &$oldPath,
                $oldDisk,
                $driveTargetChanged,
            ): Documento {
                if ($file !== null) {
                    $newPath = $file->store('uploads', 'local');
                    $oldPath = $documento->path;

                    $data['path'] = $newPath;
                    $data['disk'] = 'local';
                    $data['mime_type'] = $file->getMimeType() ?? 'application/octet-stream';
                    $data['tamanho'] = $file->getSize();
                }

                if ($file !== null || $driveTargetChanged) {
                    $data['sync_status'] = null;
                    $data['sync_error'] = null;
                    $data['drive_file_id'] = null;
                    $data['drive_link'] = null;
                    $data['synced_at'] = null;
                }

                $documento->update($data);

                if ($file !== null || $driveTargetChanged) {
                    DB::afterCommit(static function () use ($oldDisk, $oldPath): void {
                        if (is_string($oldPath) && $oldPath !== '') {
                            Storage::disk($oldDisk)->delete($oldPath);
                        }
                    });
                }

                return $documento->refresh();
            });
        } catch (Throwable $throwable) {
            if (is_string($newPath) && $newPath !== '') {
                Storage::disk('local')->delete($newPath);
            }

            throw $throwable;
        }
    }

    public function delete(Documento $documento): void
    {
        $disk = $documento->disk;
        $path = $documento->path;

        DB::transaction(static function () use ($documento, $disk, $path): void {
            $documento->delete();

            DB::afterCommit(static function () use ($disk, $path): void {
                Storage::disk($disk)->delete($path);
            });
        });
    }

    public function triggerManualSync(Documento $documento): void
    {
        DB::transaction(function () use ($documento): void {
            $documento->update([
                'sync_status' => 'pending',
                'sync_error' => null,
                'drive_file_id' => null,
                'drive_link' => null,
                'synced_at' => null,
            ]);

            DB::afterCommit(static function () use ($documento): void {
                SyncFileToDriveJob::dispatch($documento->id);
            });
        });
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function normalizeDriveTarget(array $data): array
    {
        $driveAccountId = Arr::get($data, 'drive_account_id');
        $data['drive_account_id'] = is_numeric($driveAccountId) ? (int) $driveAccountId : null;

        $driveFolderId = Arr::get($data, 'drive_folder_id');
        $data['drive_folder_id'] = is_string($driveFolderId) && trim($driveFolderId) !== ''
            ? trim($driveFolderId)
            : null;

        if ($data['drive_account_id'] === null) {
            $data['drive_folder_id'] = null;
        }

        return $data;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function driveTargetChanged(Documento $documento, array $data): bool
    {
        return ($data['drive_account_id'] ?? null) !== $documento->drive_account_id
            || ($data['drive_folder_id'] ?? null) !== $documento->drive_folder_id;
    }
}
