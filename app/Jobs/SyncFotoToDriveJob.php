<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\DriveAccount;
use App\Models\Foto;
use App\Services\AuditLogService;
use App\Services\GoogleDriveService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

final class SyncFotoToDriveJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $fotoId,
    ) {
    }

    public function handle(
        GoogleDriveService $googleDriveService,
        AuditLogService $auditLogService,
    ): void {
        $foto = Foto::query()->with(['driveAccount', 'igreja'])->find($this->fotoId);

        if ($foto === null) {
            return;
        }

        $driveAccount = $foto->driveAccount;

        if (! $driveAccount instanceof DriveAccount) {
            $message = 'Conta Google Drive nao configurada para a foto.';

            $foto->update([
                'sync_status' => 'error',
                'sync_error' => $message,
            ]);

            return;
        }

        try {
            $result = $googleDriveService->uploadFoto(
                $foto,
                $driveAccount,
                $foto->drive_folder_id,
            );

            $foto->update([
                'drive_file_id' => $result->fileId,
                'drive_link' => $result->webViewLink,
                'sync_status' => 'synced',
                'sync_error' => null,
                'synced_at' => now(),
            ]);

            $auditLogService->log(
                action: 'sync',
                module: 'fotos',
                entity: Foto::class,
                entityId: $foto->id,
                oldValues: ['sync_status' => 'pending'],
                newValues: ['sync_status' => 'synced', 'drive_file_id' => $result->fileId],
            );
        } catch (Throwable $exception) {
            $foto->update([
                'sync_status' => 'error',
                'sync_error' => $exception->getMessage(),
            ]);
        }
    }
}
