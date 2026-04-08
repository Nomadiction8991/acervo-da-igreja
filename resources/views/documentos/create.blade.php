<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('documentos.index') }}" class="text-sm opacity-75 hover:underline">← Documentos</a>
            <h1 class="section-title mt-1">Novo documento</h1>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('documentos.store') }}" enctype="multipart/form-data" class="surface panel-padding max-w-4xl space-y-5">
        @csrf
        @include('documentos.partials.form', ['documento' => null])
    </form>
</x-app-layout>
