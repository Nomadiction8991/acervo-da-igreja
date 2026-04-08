<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\IgrejaFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $codigo_controle
 * @property string $nome_fantasia
 * @property string $razao_social
 * @property ?string $matricula
 * @property ?string $cep
 * @property ?string $endereco
 * @property ?string $cidade
 * @property ?string $estado
 * @property array<string, bool>|null $visibilidade
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property ?\Illuminate\Support\Carbon $deleted_at
 * @property-read Collection<int, Foto> $fotos
 */
final class Igreja extends Model
{
    public const FIELD_VISIBILITY_MAP = [
        'codigo_controle' => 'publico_codigo_controle',
        'nome_fantasia' => 'publico_nome_fantasia',
        'razao_social' => 'publico_razao_social',
        'matricula' => 'publico_matricula',
        'cep' => 'publico_cep',
        'endereco' => 'publico_endereco',
        'cidade' => 'publico_cidade',
        'estado' => 'publico_estado',
    ];

    /** @use HasFactory<IgrejaFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $table = 'igrejas';

    protected $fillable = [
        'codigo_controle',
        'nome_fantasia',
        'razao_social',
        'matricula',
        'cep',
        'endereco',
        'cidade',
        'estado',
        'publico_codigo_controle',
        'publico_nome_fantasia',
        'publico_razao_social',
        'publico_matricula',
        'publico_cep',
        'publico_endereco',
        'publico_cidade',
        'publico_estado',
        'visibilidade',
    ];

    protected $casts = [
        'visibilidade' => 'array',
        'publico_codigo_controle' => 'boolean',
        'publico_nome_fantasia' => 'boolean',
        'publico_razao_social' => 'boolean',
        'publico_matricula' => 'boolean',
        'publico_cep' => 'boolean',
        'publico_endereco' => 'boolean',
        'publico_cidade' => 'boolean',
        'publico_estado' => 'boolean',
    ];

    /**
     * Obter fotos da igreja.
     */
    /**
     * @return HasMany<Foto, $this>
     */
    public function fotos(): HasMany
    {
        return $this->hasMany(Foto::class, 'igreja_id');
    }

    /**
     * @return HasMany<Documento, $this>
     */
    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class, 'igreja_id');
    }

    /**
     * @return HasMany<Tarefa, $this>
     */
    public function tarefas(): HasMany
    {
        return $this->hasMany(Tarefa::class, 'igreja_id');
    }

    /**
     * Obter foto principal.
     */
    public function fotoPrincipal(): ?Foto
    {
        /** @var Foto|null $foto */
        $foto = $this->fotos()
            ->where('is_principal', true)
            ->first();

        return $foto;
    }

    /**
     * Obter fotos públicas.
     *
     * @return Collection<int, Foto>
     */
    public function fotosPublicas(): Collection
    {
        /** @var Collection<int, Foto> $fotos */
        $fotos = $this->fotos()
            ->where('is_public', true)
            ->orderBy('ordem')
            ->get();

        return $fotos;
    }

    /**
     * Verificar se campo é público.
     */
    public function esCampoPublico(string $campo): bool
    {
        $column = self::FIELD_VISIBILITY_MAP[$campo] ?? null;

        if ($column !== null) {
            $value = $this->getAttribute($column);

            if ($value !== null) {
                return (bool) $value;
            }
        }

        return (bool) (($this->visibilidade ?? [])[$campo] ?? false);
    }

    /**
     * Definir campo como público.
     */
    public function definirCampoPublico(string $campo, bool $publico): void
    {
        $visibilidade = $this->visibilidade ?? [];
        $visibilidade[$campo] = $publico;
        $this->visibilidade = $visibilidade;

        $column = self::FIELD_VISIBILITY_MAP[$campo] ?? null;

        if ($column !== null) {
            $this->setAttribute($column, $publico);
        }
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
            $q->where('codigo_controle', 'like', $term)
                ->orWhere('nome_fantasia', 'like', $term)
                ->orWhere('razao_social', 'like', $term)
                ->orWhere('cidade', 'like', $term)
                ->orWhere('endereco', 'like', $term);
        });
    }

    /**
     * @return MorphToMany<Tag>
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    /**
     * Filtrar por tag.
     *
     * @param Builder<self> $query
     */
    public function scopeByTag(Builder $query, string $slug): Builder
    {
        return $query->whereHas('tags', static function (Builder $q) use ($slug): void {
            $q->where('slug', $slug);
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function getPublicData(): array
    {
        $data = [
            'id' => $this->id,
        ];

        foreach (self::FIELD_VISIBILITY_MAP as $field => $column) {
            if (! $this->esCampoPublico($field)) {
                continue;
            }

            $value = $this->getAttribute($field);

            if ($value !== null && $value !== '') {
                $data[$field] = $value;
            }
        }

        return $data;
    }

    protected static function newFactory(): IgrejaFactory
    {
        return IgrejaFactory::new();
    }
}
