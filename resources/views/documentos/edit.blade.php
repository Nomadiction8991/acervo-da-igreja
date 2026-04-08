<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('documentos.show', $documento) }}" class="text-sm opacity-75 hover:underline">← {{ $documento->titulo }}</a>
            <h1 class="section-title mt-1">Editar documento</h1>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('documentos.update', $documento) }}" enctype="multipart/form-data" class="surface panel-padding max-w-4xl space-y-5">
        @csrf
        @method('PUT')
        @include('documentos.partials.form', ['documento' => $documento])
    </form>
</x-app-layout>
