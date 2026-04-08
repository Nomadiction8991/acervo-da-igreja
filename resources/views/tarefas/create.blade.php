<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('tarefas.index') }}" class="text-sm opacity-75 hover:underline">← Tarefas</a>
            <h1 class="section-title mt-1">Nova tarefa</h1>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('tarefas.store') }}" class="surface panel-padding max-w-3xl space-y-5">
        @csrf
        @include('tarefas.partials.form', ['tarefa' => null])
    </form>
</x-app-layout>
