<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Tarefa;
use App\Models\User;

final class TarefaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('tarefas.visualizar');
    }

    public function view(User $user, Tarefa $tarefa): bool
    {
        return $user->hasPermission('tarefas.visualizar');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('tarefas.criar');
    }

    public function update(User $user, Tarefa $tarefa): bool
    {
        return $user->hasPermission('tarefas.editar');
    }

    public function delete(User $user, Tarefa $tarefa): bool
    {
        return $user->hasPermission('tarefas.deletar');
    }
}
