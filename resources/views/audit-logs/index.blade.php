<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="eyebrow">Rastreabilidade</p>
            <h1 class="section-title mt-1">Auditoria</h1>
        </div>
    </x-slot>

    <div class="surface panel-padding mb-6">
        <form method="GET" class="grid gap-4 md:grid-cols-5">
            <div>
                <label class="field-block__label" for="user_id">Usuario</label>
                <select id="user_id" name="user_id" class="field-control">
                    <option value="">Todos</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="field-block__label" for="acao">Acao</label>
                <select id="acao" name="acao" class="field-control">
                    <option value="">Todas</option>
                    @foreach ($acoes as $acao)
                        <option value="{{ $acao }}" @selected(request('acao') === $acao)>{{ $acao }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="field-block__label" for="modulo">Modulo</label>
                <select id="modulo" name="modulo" class="field-control">
                    <option value="">Todos</option>
                    @foreach ($modulos as $modulo)
                        <option value="{{ $modulo }}" @selected(request('modulo') === $modulo)>{{ $modulo }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="field-block__label" for="data_inicial">Data inicial</label>
                <input id="data_inicial" name="data_inicial" type="date" class="field-control" value="{{ request('data_inicial') }}">
            </div>
            <div>
                <label class="field-block__label" for="data_final">Data final</label>
                <input id="data_final" name="data_final" type="date" class="field-control" value="{{ request('data_final') }}">
            </div>
            <div class="md:col-span-5 flex gap-3">
                <button type="submit" class="button button-primary">Filtrar</button>
                <a href="{{ route('audit-logs.index') }}" class="button button-muted">Limpar</a>
            </div>
        </form>
    </div>

    <div class="space-y-4">
        @forelse ($logs as $log)
            <article class="surface panel-padding">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="font-semibold">{{ $log->acao }} · {{ $log->modulo }}</p>
                        <p class="text-sm text-[var(--text-secondary)]">{{ $log->user?->name ?? 'Sistema' }} · {{ $log->entidade }} #{{ $log->entidade_id }}</p>
                    </div>
                    <span class="text-xs text-[var(--text-secondary)]">{{ $log->created_at->format('d/m/Y H:i:s') }}</span>
                </div>
                <div class="grid gap-4 mt-4 lg:grid-cols-2">
                    <div>
                        <p class="field-block__label mb-2">Antes</p>
                        <pre class="text-xs overflow-auto bg-[var(--surface-inset)] rounded-lg p-3">{{ json_encode($log->old_values ?? $log->antes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                    <div>
                        <p class="field-block__label mb-2">Depois</p>
                        <pre class="text-xs overflow-auto bg-[var(--surface-inset)] rounded-lg p-3">{{ json_encode($log->new_values ?? $log->depois, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                </div>
            </article>
        @empty
            <div class="surface panel-padding text-center text-[var(--text-secondary)]">Nenhum log encontrado.</div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $logs->links() }}
    </div>
</x-app-layout>
