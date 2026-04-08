<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $nome
 * @property string $slug
 * @property ?string $descricao
 * @property string $cor
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property ?\Illuminate\Support\Carbon $deleted_at
 * @property-read Collection<int, Igreja> $igrejas
 */
final class Tag extends Model
{
    use SoftDeletes;

    protected $table = 'tags';

    protected $fillable = [
        'nome',
        'slug',
        'descricao',
        'cor',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(static function (self $tag): void {
            if (!$tag->slug) {
                $tag->slug = Str::slug($tag->nome);
            }
        });
    }

    /**
     * @return MorphToMany<Igreja>
     */
    public function igrejas(): MorphToMany
    {
        return $this->morphedByMany(Igreja::class, 'taggable');
    }
}
