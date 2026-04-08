<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('drive-accounts.show', $driveAccount) }}" class="text-sm opacity-75 hover:underline">← {{ $driveAccount->nome }}</a>
            <h1 class="section-title mt-1">Editar conta Google Drive</h1>
        </div>
    </x-slot>

    @if ($errors->any())
        <div class="status-banner status-banner--danger mb-4">
            Revise os campos e tente novamente.
        </div>
    @endif

    <form method="POST" action="{{ route('drive-accounts.update', $driveAccount) }}" class="surface panel-padding max-w-4xl space-y-5">
        @csrf
        @method('PATCH')
        @include('drive-accounts.partials.form')
    </form>
</x-app-layout>
