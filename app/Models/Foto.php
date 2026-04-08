<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\FotoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $igreja_id
 * @property ?int $drive_account_id
 * @property ?string $drive_folder_id
 * @property string $caminho
 * @property string $disk
 * @property string $nome_original
 * @property string $mime_type
 * @property int $tamanho
 * @property bool $is_public
 * @property bool $is_principal
 * @property int $ordem
 * @property ?string $drive_file_id
 * @property ?string $drive_link
 * @property ?string $sync_status
 * @property ?string $sync_error
 * @property ?\Illuminate\Support\Carbon $synced_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property ?\Illuminate\Support\Carbon $deleted_at
 * @property-read Igreja $igreja
 * @property-read ?DriveAccount $driveAccount
 */
final class Foto extends Model
{
    /** @use HasFactory<FotoFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $table = 'fotos';

    protected $fillable = [
        'igreja_id',
        'drive_account_id',
        'drive_folder_id',
        'caminho',
        'disk',
        'nome_original',
        'mime_type',
        'tamanho',
        'is_public',
        'is_principal',
        'ordem',
        'drive_file_id',
        'drive_link',
        'sync_status',
        'sync_error',
        'synced_at',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'is_principal' => 'boolean',
        'synced_at' => 'datetime',
    ];

    /**
     * Igreja à qual a foto pertence.
     *
     * @return BelongsTo<Igreja, $this>
     */
    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class, 'igreja_id');
    }

    /**
     * @return BelongsTo<DriveAccount, $this>
     */
    public function driveAccount(): BelongsTo
    {
        return $this->belongsTo(DriveAccount::class, 'drive_account_id');
    }

    public function isPublic(): bool
    {
        return $this->is_public;
    }

    public function isPrincipal(): bool
    {
        return $this->is_principal;
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

    protected static function newFactory(): FotoFactory
    {
        return FotoFactory::new();
    }
}
