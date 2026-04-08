<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property ?int $user_id
 * @property string $acao
 * @property string $modulo
 * @property string $entidade
 * @property int $entidade_id
 * @property ?array<string, mixed> $old_values
 * @property ?array<string, mixed> $new_values
 * @property ?array<string, mixed> $antes
 * @property ?array<string, mixed> $depois
 * @property ?string $ip_address
 * @property ?string $user_agent
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read ?User $user
 */
final class AuditLog extends Model
{
    protected $table = 'audit_logs';

    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'acao',
        'modulo',
        'entidade',
        'entidade_id',
        'old_values',
        'new_values',
        'antes',
        'depois',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'antes' => 'array',
        'depois' => 'array',
    ];

    /**
     * Usuário que realizou a ação.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
