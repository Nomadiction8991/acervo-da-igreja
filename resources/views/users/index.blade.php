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

    <div class="surface rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-[var(--border-subtle)]">
                    <th class="px-4 py-3 text-left">Nome</th>
                    <th class="px-4 py-3 text-left hidden md:table-cell">Email</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-right">Acoes</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr class="border-b border-[var(--border-subtle)] last:border-0">
                        <td class="px-4 py-4">
                            <a href="{{ route('users.show', $user) }}" class="font-semibold hover:underline">{{ $user->name }}</a>
                            <div class="text-xs text-[var(--text-secondary)] mt-1">{{ $user->is_admin ? 'Administrador' : 'Permissoes personalizadas' }}</div>
                        </td>
                        <td class="px-4 py-4 hidden md:table-cell">{{ $user->email }}</td>
                        <td class="px-4 py-4">{{ $user->is_active ? 'Ativo' : 'Inativo' }}</td>
                        <td class="px-4 py-4">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('users.show', $user) }}" class="button button-muted text-xs">Ver</a>
                                <a href="{{ route('users.edit', $user) }}" class="button button-ghost text-xs">Editar</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-[var(--text-secondary)]">Nenhum usuario encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $users->links() }}
    </div>
</x-app-layout>
