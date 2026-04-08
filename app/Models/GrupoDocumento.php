<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $nome
 * @property ?string $descricao
 * @property bool $publico_padrao
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read Collection<int, Documento> $documentos
 */
final class GrupoDocumento extends Model
{
    protected $table = 'grupo_documentos';

    protected $fillable = [
        'nome',
        'descricao',
        'publico_padrao',
    ];

    protected $casts = [
        'publico_padrao' => 'boolean',
    ];

    /**
     * @return HasMany<Documento, $this>
     */
    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class, 'grupo_documento_id');
    }
}
