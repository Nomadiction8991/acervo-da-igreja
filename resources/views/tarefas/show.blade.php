<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <a href="{{ route('tarefas.index') }}" class="text-sm opacity-75 hover:underline">← Tarefas</a>
                <h1 class="section-title mt-1">{{ $tarefa->titulo }}</h1>
            </div>
            <a href="{{ route('tarefas.edit', $tarefa) }}" class="button button-primary">Editar</a>
        </div>
    </x-slot>

    <div class="surface panel-padding max-w-3xl space-y-4">
        <div class="data-row">
            <span class="data-row__label">Igreja</span>
            <span class="data-row__value">{{ $tarefa->igreja->nome_fantasia }}</span>
        </div>
        <div class="data-row">
            <span class="data-row__label">Responsavel</span>
            <span class="data-row__value">{{ $tarefa->user?->name ?? 'Sem responsavel' }}</span>
        </div>
        <div class="data-row">
            <span class="data-row__label">Status</span>
            <span class="data-row__value">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium" style="background-color: {{ $tarefa->status->bgColor() }}; color: {{ $tarefa->status->color() }};">
                    <span class="w-2 h-2 rounded-full" style="background-color: {{ $tarefa->status->color() }};"></span>
                    {{ $tarefa->status->label() }}
                </span>
            </span>
        </div>
        <div class="data-row">
            <span class="data-row__label">Prioridade</span>
            <span class="data-row__value">{{ $tarefa->prioridade->label() }}</span>
        </div>
        <div class="data-row">
            <span class="data-row__label">Prazo</span>
            <span class="data-row__value">{{ $tarefa->due_at?->format('d/m/Y H:i') ?? 'Nao definido' }}</span>
        </div>
        @if ($tarefa->descricao)
            <div>
                <p class="field-block__label mb-2">Descricao</p>
                <p class="text-sm text-[var(--text-secondary)]">{{ $tarefa->descricao }}</p>
            </div>
        @endif
    </div>
</x-app-layout>
