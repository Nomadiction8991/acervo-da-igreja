<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $nome
 * @property ?string $email
 * @property string $provider
 * @property ?string $folder_id
 * @property bool $is_active
 * @property ?string $client_id
 * @property ?string $client_secret
 * @property ?string $refresh_token
 * @property ?array<string, mixed> $metadata
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read Collection<int, Documento> $documentos
 * @property-read Collection<int, Foto> $fotos
 * @property-read Collection<int, FileSyncLog> $fileSyncLogs
 */
final class DriveAccount extends Model
{
    protected $table = 'drive_accounts';

    protected $fillable = [
        'nome',
        'email',
        'provider',
        'folder_id',
        'is_active',
        'client_id',
        'client_secret',
        'refresh_token',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'client_id' => 'encrypted',
        'client_secret' => 'encrypted',
        'refresh_token' => 'encrypted',
        'metadata' => 'array',
    ];

    /**
     * @return HasMany<FileSyncLog, $this>
     */
    public function fileSyncLogs(): HasMany
    {
        return $this->hasMany(FileSyncLog::class, 'drive_account_id');
    }

    /**
     * @return HasMany<Documento, $this>
     */
    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class, 'drive_account_id');
    }

    /**
     * @return HasMany<Foto, $this>
     */
    public function fotos(): HasMany
    {
        return $this->hasMany(Foto::class, 'drive_account_id');
    }
}
