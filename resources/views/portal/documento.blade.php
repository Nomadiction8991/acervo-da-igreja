<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <a href="{{ route('portal.show', $documento->igreja) }}" class="text-sm opacity-75 hover:underline">← {{ $documento->igreja->nome_fantasia }}</a>
                <h1 class="section-title mt-1">{{ $documento->titulo }}</h1>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('files.documentos.preview', $documento) }}" target="_blank" class="button button-primary">Abrir em nova aba</a>
                <a href="{{ route('files.documentos.show', $documento) }}" class="button button-muted">Baixar</a>
            </div>
        </div>
    </x-slot>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">
        <div class="surface panel-padding">
            @if ($documento->supportsInlinePreview())
                <iframe
                    src="{{ route('files.documentos.preview', $documento) }}"
                    title="{{ $documento->titulo }}"
                    class="w-full min-h-[75vh] rounded-xl border border-[var(--border-subtle)] bg-white"
                ></iframe>
            @else
                <div class="rounded-xl border border-[var(--border-subtle)] p-8 text-center">
                    <p class="text-lg font-semibold">Visualizacao integrada indisponivel para este formato.</p>
                    <p class="mt-3 text-sm text-[var(--text-secondary)]">
                        Abra o arquivo em uma nova aba ou faca o download para consultar o documento.
                    </p>
                    <div class="mt-5 flex justify-center gap-3">
                        <a href="{{ route('files.documentos.preview', $documento) }}" target="_blank" class="button button-primary">Abrir arquivo</a>
                        <a href="{{ route('files.documentos.show', $documento) }}" class="button button-muted">Baixar</a>
                    </div>
                </div>
            @endif
        </div>

        <aside class="space-y-4">
            <div class="surface panel-padding">
                <p class="eyebrow mb-3">Documento publico</p>
                <div class="space-y-3 text-sm">
                    <div class="data-row">
                        <span class="data-row__label">Igreja</span>
                        <span class="data-row__value">{{ $documento->igreja->nome_fantasia }}</span>
                    </div>
                    <div class="data-row">
                        <span class="data-row__label">Grupo</span>
                        <span class="data-row__value">{{ $documento->grupoDocumento?->nome ?? 'Sem grupo' }}</span>
                    </div>
                    <div class="data-row">
                        <span class="data-row__label">Tipo</span>
                        <span class="data-row__value">{{ $documento->tipo }}</span>
                    </div>
                    <div class="data-row">
                        <span class="data-row__label">Arquivo</span>
                        <span class="data-row__value">{{ $documento->fileName() }}</span>
                    </div>
                </div>
            </div>

            @if ($documento->descricao)
                <div class="surface panel-padding">
                    <p class="eyebrow mb-3">Descricao</p>
                    <p class="text-sm text-[var(--text-secondary)]">{{ $documento->descricao }}</p>
                </div>
            @endif
        </aside>
    </div>
</x-app-layout>
