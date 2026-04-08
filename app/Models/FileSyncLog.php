<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property ?int $documento_id
 * @property ?int $drive_account_id
 * @property ?int $user_id
 * @property string $status
 * @property ?string $message
 * @property ?string $drive_file_id
 * @property ?string $drive_link
 * @property ?array<string, mixed> $payload
 * @property ?\Illuminate\Support\Carbon $attempted_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read ?Documento $documento
 * @property-read ?DriveAccount $driveAccount
 * @property-read ?User $user
 */
final class FileSyncLog extends Model
{
    protected $table = 'file_sync_logs';

    protected $fillable = [
        'documento_id',
        'drive_account_id',
        'user_id',
        'status',
        'message',
        'drive_file_id',
        'drive_link',
        'payload',
        'attempted_at',
    ];

    protected $casts = [
        'attempted_at' => 'datetime',
        'payload' => 'array',
    ];

    /**
     * @return BelongsTo<Documento, $this>
     */
    public function documento(): BelongsTo
    {
        return $this->belongsTo(Documento::class, 'documento_id');
    }

    /**
     * @return BelongsTo<DriveAccount, $this>
     */
    public function driveAccount(): BelongsTo
    {
        return $this->belongsTo(DriveAccount::class, 'drive_account_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
