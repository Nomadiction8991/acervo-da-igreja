<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('users.index') }}" class="text-sm opacity-75 hover:underline">← Usuarios</a>
            <h1 class="section-title mt-1">Novo usuario</h1>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('users.store') }}" class="surface panel-padding max-w-4xl space-y-5">
        @csrf
        @include('users.partials.form', ['user' => null, 'permissions' => $permissions])
    </form>
</x-app-layout>
