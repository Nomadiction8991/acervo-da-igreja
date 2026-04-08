<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $igreja_id
 * @property ?int $user_id
 * @property string $titulo
 * @property ?string $descricao
 * @property TaskStatus $status
 * @property TaskPriority $prioridade
 * @property ?\Illuminate\Support\Carbon $due_at
 * @property ?\Illuminate\Support\Carbon $completed_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property ?\Illuminate\Support\Carbon $deleted_at
 * @property-read Igreja $igreja
 * @property-read ?User $user
 */
final class Tarefa extends Model
{
    use SoftDeletes;

    protected $table = 'tarefas';

    protected $fillable = [
        'igreja_id',
        'user_id',
        'titulo',
        'descricao',
        'status',
        'prioridade',
        'due_at',
        'completed_at',
    ];

    protected $casts = [
        'status' => TaskStatus::class,
        'prioridade' => TaskPriority::class,
        'due_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<Igreja, $this>
     */
    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class, 'igreja_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
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
                ->orWhere('descricao', 'like', $term);
        });
    }
}
