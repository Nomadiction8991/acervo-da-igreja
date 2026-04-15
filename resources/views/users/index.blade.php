<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="eyebrow">Acesso</p>
                <h1 class="section-title mt-1">Usuarios</h1>
            </div>
            @can('create', App\Models\User::class)
                <a href="{{ route('users.create') }}" class="button button-primary">+ Novo usuario</a>
            @endcan
        </div>
    </x-slot>

    @if (session('success'))
        <div class="status-banner status-banner--success mb-4">{{ session('success') }}</div>
    @endif

    <div class="resource-table-shell">
        <div class="resource-table-scroll">
        <table class="resource-table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th class="hidden md:table-cell">Email</th>
                    <th>Status</th>
                    <th>Acoes</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>
                            <div class="resource-table__main">
                                <a href="{{ route('users.show', $user) }}" class="resource-table__title">{{ $user->name }}</a>
                                <div class="resource-table__meta">{{ $user->is_admin ? 'Administrador' : 'Permissoes personalizadas' }}</div>
                            </div>
                        </td>
                        <td class="hidden md:table-cell">
                            <span class="resource-table__meta">{{ $user->email }}</span>
                        </td>
                        <td>
                            <span class="resource-table__status {{ $user->is_active ? 'resource-table__status--positive' : 'resource-table__status--muted' }}">
                                {{ $user->is_active ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td>
                            <div class="resource-table__actions">
                                <a href="{{ route('users.show', $user) }}" class="button button-muted text-xs">Ver</a>
                                @unless ($user->is_admin)
                                    <a href="{{ route('users.edit', $user) }}" class="button button-ghost text-xs">Editar</a>
                                @endunless
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="resource-table__empty">Nenhum usuario encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $users->links() }}
    </div>
</x-app-layout>
