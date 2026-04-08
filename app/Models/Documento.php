<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $igreja_id
 * @property ?int $grupo_documento_id
 * @property ?int $user_id
 * @property ?int $drive_account_id
 * @property ?string $drive_folder_id
 * @property string $titulo
 * @property ?string $descricao
 * @property string $path
 * @property string $disk
 * @property string $tipo
 * @property string $mime_type
 * @property int $tamanho
 * @property bool $publico
 * @property ?string $drive_file_id
 * @property ?string $drive_link
 * @property ?string $sync_status
 * @property ?string $sync_error
 * @property ?\Illuminate\Support\Carbon $synced_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property ?\Illuminate\Support\Carbon $deleted_at
 * @property-read Igreja $igreja
 * @property-read ?GrupoDocumento $grupoDocumento
 * @property-read ?User $user
 * @property-read ?DriveAccount $driveAccount
 * @property-read Collection<int, FileSyncLog> $fileSyncLogs
 */
final class Documento extends Model
{
    use SoftDeletes;

    protected $table = 'documentos';

    protected $fillable = [
        'igreja_id',
        'grupo_documento_id',
        'user_id',
        'drive_account_id',
        'drive_folder_id',
        'titulo',
        'descricao',
        'path',
        'disk',
        'tipo',
        'mime_type',
        'tamanho',
        'publico',
        'drive_file_id',
        'drive_link',
        'sync_status',
        'sync_error',
        'synced_at',
    ];

    protected $casts = [
        'publico' => 'boolean',
        'synced_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<Igreja, $this>
     */
    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class, 'igreja_id');
    }

    /**
     * @return BelongsTo<GrupoDocumento, $this>
     */
    public function grupoDocumento(): BelongsTo
    {
        return $this->belongsTo(GrupoDocumento::class, 'grupo_documento_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo<DriveAccount, $this>
     */
    public function driveAccount(): BelongsTo
    {
        return $this->belongsTo(DriveAccount::class, 'drive_account_id');
    }

    /**
     * @return HasMany<FileSyncLog, $this>
     */
    public function fileSyncLogs(): HasMany
    {
        return $this->hasMany(FileSyncLog::class, 'documento_id');
    }

    public function fileName(): string
    {
        $baseName = trim($this->titulo) !== '' ? $this->titulo : 'documento-'.$this->id;
        $extension = pathinfo($this->path, PATHINFO_EXTENSION);

        if ($extension === '') {
            return $baseName;
        }

        if (Str::lower(pathinfo($baseName, PATHINFO_EXTENSION)) === Str::lower($extension)) {
            return $baseName;
        }

        return $baseName.'.'.$extension;
    }

    public function supportsInlinePreview(): bool
    {
        return str_starts_with($this->mime_type, 'image/')
            || str_starts_with($this->mime_type, 'text/')
            || in_array($this->mime_type, [
                'application/pdf',
                'application/json',
            ], true);
    }

    public function driveStatusLabel(): string
    {
        return match ($this->sync_status) {
            'pending' => 'Pendente',
            'synced' => 'Sincronizado',
            'error' => 'Com erro',
            default => 'Sem sincronizacao',
        };
    }

    public function driveStatusChipVariant(): string
    {
        return $this->sync_status === 'error' ? 'private' : 'public';
    }

    /**
     * Filtrar por termo de busca.
     *
     * @param Builder<self> $query
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (!$term) {
            return $query;
        }

        $term = "%{$term}%";

        return $query->where(function (Builder $q) use ($term): void {
            $q->where('titulo', 'like', $term)
                ->orWhere('descricao', 'like', $term)
                ->orWhere('tipo', 'like', $term);
        });
    }
}
