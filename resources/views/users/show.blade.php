<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <a href="{{ route('users.index') }}" class="text-sm opacity-75 hover:underline">← Usuarios</a>
                <h1 class="section-title mt-1">{{ $user->name }}</h1>
            </div>
            <a href="{{ route('users.edit', $user) }}" class="button button-primary">Editar</a>
        </div>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
        <div class="surface panel-padding space-y-4">
            <div class="data-row">
                <span class="data-row__label">Email</span>
                <span class="data-row__value">{{ $user->email }}</span>
            </div>
            <div class="data-row">
                <span class="data-row__label">Perfil</span>
                <span class="data-row__value">{{ $user->is_admin ? 'Administrador' : 'Padrao' }}</span>
            </div>
            <div class="data-row">
                <span class="data-row__label">Status</span>
                <span class="data-row__value">{{ $user->is_active ? 'Ativo' : 'Inativo' }}</span>
            </div>
            <div>
                <p class="field-block__label mb-3">Permissoes</p>
                <div class="flex flex-wrap gap-2">
                    @forelse ($user->permissions as $permission)
                        <span class="chip chip--public">{{ $permission->slug }}</span>
                    @empty
                        <span class="text-sm text-[var(--text-secondary)]">Sem permissoes vinculadas.</span>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="surface panel-padding">
                <p class="eyebrow mb-3">Atividade</p>
                <p class="text-sm text-[var(--text-secondary)]">Tarefas: {{ $user->tarefas->count() }}</p>
                <p class="text-sm text-[var(--text-secondary)]">Documentos: {{ $user->documentos->count() }}</p>
            </div>

            <div class="surface panel-padding border border-red-900/30">
                <p class="eyebrow text-red-400 mb-3">Zona de perigo</p>
                <form method="POST" action="{{ route('users.destroy', $user) }}">
                    @csrf
                    @method('DELETE')
                    <button class="button button-ghost w-full border-red-900/40 text-red-400" type="submit" onclick="return confirm('Remover este usuario?')">
                        Deletar usuario
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
