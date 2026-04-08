<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('users.show', $user) }}" class="text-sm opacity-75 hover:underline">← {{ $user->name }}</a>
            <h1 class="section-title mt-1">Editar usuario</h1>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('users.update', $user) }}" class="surface panel-padding max-w-4xl space-y-5">
        @csrf
        @method('PUT')
        @include('users.partials.form', ['user' => $user])
    </form>
</x-app-layout>
