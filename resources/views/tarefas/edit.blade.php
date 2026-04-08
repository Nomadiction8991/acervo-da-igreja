<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('tarefas.show', $tarefa) }}" class="text-sm opacity-75 hover:underline">← {{ $tarefa->titulo }}</a>
            <h1 class="section-title mt-1">Editar tarefa</h1>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('tarefas.update', $tarefa) }}" class="surface panel-padding max-w-3xl space-y-5">
        @csrf
        @method('PUT')
        @include('tarefas.partials.form', ['tarefa' => $tarefa])
    </form>
</x-app-layout>
