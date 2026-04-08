<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property ?\Illuminate\Support\Carbon $email_verified_at
 * @property string $password
 * @property ?string $remember_token
 * @property bool $is_admin
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read Collection<int, Permission> $permissions
 */
final class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Permissões do usuário.
     */
    /**
     * @return BelongsToMany<Permission, $this, UserPermission>
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_permissions')
            ->using(UserPermission::class);
    }

    /**
     * @return HasMany<Tarefa, $this>
     */
    public function tarefas(): HasMany
    {
        return $this->hasMany(Tarefa::class);
    }

    /**
     * @return HasMany<Documento, $this>
     */
    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class);
    }

    /**
     * @return HasMany<AuditLog, $this>
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Verificar se usuário é administrador.
     */
    public function ehAdmin(): bool
    {
        return $this->is_admin;
    }

    /**
     * Verificar se usuário está ativo.
     */
    public function estaAtivo(): bool
    {
        return $this->is_active;
    }

    /**
     * Verificar se usuário tem permissão específica.
     */
    public function hasPermission(string $slug): bool
    {
        if (! $this->estaAtivo()) {
            return false;
        }

        if ($this->ehAdmin()) {
            return true;
        }

        return $this->permissions()
            ->where(static function ($query) use ($slug): void {
                $query
                    ->where('slug', $slug)
                    ->orWhere('nome', $slug);
            })
            ->exists();
    }

    /**
     * Alias legado.
     */
    public function temPermissao(string $permissao): bool
    {
        return $this->hasPermission($permissao);
    }
}
