<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('grupo-documentos.show', $grupoDocumento) }}" class="text-sm opacity-75 hover:underline">← {{ $grupoDocumento->nome }}</a>
            <h1 class="section-title mt-1">Editar grupo de documentos</h1>
        </div>
    </x-slot>

    @if ($errors->any())
        <div class="status-banner status-banner--danger mb-4">
            Revise os campos do formulario e tente novamente.
        </div>
    @endif

    <form method="POST" action="{{ route('grupo-documentos.update', $grupoDocumento) }}" class="surface panel-padding max-w-3xl space-y-5">
        @csrf
        @method('PATCH')
        @include('grupo-documentos.partials.form')
    </form>
</x-app-layout>
