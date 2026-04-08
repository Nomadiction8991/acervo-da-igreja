<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property ?string $slug
 * @property ?string $nome
 * @property ?string $descricao
 * @property string $modulo
 * @property string $acao
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read Collection<int, User> $users
 */
final class Permission extends Model
{
    protected $table = 'permissions';

    protected $fillable = [
        'slug',
        'nome',
        'descricao',
        'modulo',
        'acao',
    ];

    protected static function booted(): void
    {
        static::saving(static function (self $permission): void {
            $slug = $permission->slug ?: $permission->nome;

            if ($slug === null || $slug === '') {
                $slug = $permission->modulo.'.'.$permission->acao;
            }

            $permission->slug = $slug;
            $permission->nome = $permission->nome ?: $slug;
        });
    }

    /**
     * Usuários com esta permissão.
     *
     * @return BelongsToMany<User, $this, UserPermission>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_permissions')
            ->using(UserPermission::class);
    }
}
