<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Documento;
use App\Models\DriveAccount;
use App\Models\FileSyncLog;
use App\Services\AuditLogService;
use App\Services\GoogleDriveService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

final class SyncFileToDriveJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $documentoId,
    ) {
    }

    public function handle(
        GoogleDriveService $googleDriveService,
        AuditLogService $auditLogService,
    ): void {
        $documento = Documento::query()->with('driveAccount')->find($this->documentoId);

        if ($documento === null) {
            return;
        }

        $driveAccount = $documento->driveAccount;

        if (! $driveAccount instanceof DriveAccount) {
            $message = 'Conta Google Drive selecionada para o documento nao foi encontrada.';

            $documento->update([
                'sync_status' => 'error',
                'sync_error' => $message,
            ]);

            FileSyncLog::query()->create([
                'documento_id' => $documento->id,
                'drive_account_id' => null,
                'user_id' => $documento->user_id,
                'status' => 'error',
                'message' => $message,
                'payload' => ['documento_id' => $documento->id],
                'attempted_at' => now(),
            ]);

            return;
        }

        try {
            $result = $googleDriveService->upload(
                $documento,
                $driveAccount,
                $documento->drive_folder_id,
            );

            $documento->update([
                'drive_file_id' => $result->fileId,
                'drive_link' => $result->webViewLink,
                'sync_status' => 'synced',
                'sync_error' => null,
                'synced_at' => now(),
            ]);

            FileSyncLog::query()->create([
                'documento_id' => $documento->id,
                'drive_account_id' => $driveAccount->id,
                'user_id' => $documento->user_id,
                'status' => 'synced',
                'message' => 'Arquivo sincronizado com sucesso.',
                'drive_file_id' => $result->fileId,
                'drive_link' => $result->webViewLink,
                'payload' => [
                    'documento_id' => $documento->id,
                    'drive_folder_id' => $documento->drive_folder_id,
                ],
                'attempted_at' => now(),
            ]);

            $auditLogService->log(
                action: 'sync',
                module: 'documentos',
                entity: Documento::class,
                entityId: $documento->id,
                oldValues: ['sync_status' => 'pending'],
                newValues: ['sync_status' => 'synced', 'drive_file_id' => $result->fileId],
            );
        } catch (Throwable $exception) {
            $documento->update([
                'sync_status' => 'error',
                'sync_error' => $exception->getMessage(),
            ]);

            FileSyncLog::query()->create([
                'documento_id' => $documento->id,
                'drive_account_id' => $driveAccount->id,
                'user_id' => $documento->user_id,
                'status' => 'error',
                'message' => $exception->getMessage(),
                'payload' => [
                    'documento_id' => $documento->id,
                    'drive_folder_id' => $documento->drive_folder_id,
                ],
                'attempted_at' => now(),
            ]);
        }
    }
}
