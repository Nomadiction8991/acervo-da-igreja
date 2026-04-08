<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\Permission;
use App\Models\User;
use App\Services\UserManagementService;
use DomainException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class UserController extends Controller
{
    use AuthorizesRequests;

    public function index(): View
    {
        $this->authorize('viewAny', User::class);

        $users = User::query()
            ->with('permissions')
            ->orderBy('name')
            ->paginate(20);

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        return view('users.create', [
            'permissions' => Permission::query()->orderBy('modulo')->orderBy('slug')->get()->groupBy('modulo'),
        ]);
    }

    public function store(StoreUserRequest $request, UserManagementService $service): RedirectResponse
    {
        $this->authorize('create', User::class);

        $user = $service->store([
            ...$request->safe()->except(['password_confirmation']),
            'is_admin' => $request->boolean('is_admin'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('users.show', $user)
            ->with('success', 'Usuario criado com sucesso.');
    }

    public function show(User $user): View
    {
        $this->authorize('view', $user);

        $user->load(['permissions', 'tarefas.igreja', 'documentos']);

        return view('users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        $user->load('permissions');

        return view('users.edit', [
            'user' => $user,
            'permissions' => Permission::query()->orderBy('modulo')->orderBy('slug')->get()->groupBy('modulo'),
        ]);
    }

    public function update(
        UpdateUserRequest $request,
        User $user,
        UserManagementService $service,
    ): RedirectResponse {
        $this->authorize('update', $user);

        $service->update($user, [
            ...$request->safe()->except(['password_confirmation']),
            'is_admin' => $request->boolean('is_admin'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('users.show', $user)
            ->with('success', 'Usuario atualizado com sucesso.');
    }

    public function destroy(User $user, UserManagementService $service): RedirectResponse
    {
        $this->authorize('delete', $user);

        /** @var User $actingUser */
        $actingUser = request()->user();

        try {
            $service->delete($user, $actingUser);
        } catch (DomainException $exception) {
            return back()->withErrors(['user' => $exception->getMessage()]);
        }

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuario removido com sucesso.');
    }
}
