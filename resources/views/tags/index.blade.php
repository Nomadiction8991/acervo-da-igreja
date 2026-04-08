<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="eyebrow">Organização</p>
                <h1 class="section-title mt-1">Tags/Labels</h1>
            </div>
            @if (auth()->user()?->hasPermission('tags.criar'))
                <a href="{{ route('tags.create') }}" class="button button-primary">+ Nova Tag</a>
            @endif
        </div>
    </x-slot>

    @if (session('success'))
        <div class="status-banner status-banner--success mb-4">{{ session('success') }}</div>
    @endif

    <div class="surface rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-[var(--border-subtle)]">
                    <th class="px-5 py-3 text-left">Nome</th>
                    <th class="px-5 py-3 text-left hidden md:table-cell">Descrição</th>
                    <th class="px-5 py-3 text-left">Igrejas</th>
                    <th class="px-5 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tags as $tag)
                    <tr class="border-b border-[var(--border-subtle)] last:border-0">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <span class="w-3 h-3 rounded-full" style="background-color: {{ $tag->cor }}"></span>
                                <span class="font-semibold">{{ $tag->nome }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4 hidden md:table-cell text-[var(--text-secondary)]">{{ $tag->descricao }}</td>
                        <td class="px-5 py-4">
                            <span class="text-sm bg-[var(--surface-inset)] px-2 py-1 rounded">
                                {{ $tag->igrejas_count }} igreja(s)
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex justify-end gap-2">
                                @if (auth()->user()?->hasPermission('tags.editar'))
                                    <a href="{{ route('tags.edit', $tag) }}" class="button button-ghost text-xs">Editar</a>
                                @endif
                                @if (auth()->user()?->hasPermission('tags.deletar'))
                                    <form method="POST" action="{{ route('tags.destroy', $tag) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="button button-ghost text-xs text-red-400" onclick="return confirm('Tem certeza?')">Remover</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center text-[var(--text-secondary)]">Nenhuma tag criada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $tags->links() }}
    </div>
</x-app-layout>
