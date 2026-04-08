<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('drive-accounts.index') }}" class="text-sm opacity-75 hover:underline">← Contas Google Drive</a>
            <h1 class="section-title mt-1">Nova conta Google Drive</h1>
        </div>
    </x-slot>

    @if ($errors->any())
        <div class="status-banner status-banner--danger mb-4">
            Revise os campos obrigatorios e tente novamente.
        </div>
    @endif

    <div class="surface panel-padding max-w-4xl mb-5">
        <p class="eyebrow">Dois jeitos de configurar</p>
        <div class="mt-3 space-y-2 text-sm text-[var(--text-secondary)]">
            <p>Manual: informe `client_id`, `client_secret` e `refresh_token` diretamente no formulario.</p>
            <p>OAuth: salve a conta primeiro e depois clique em `Conectar com Google` na tela de detalhes.</p>
            <p>
                Status do OAuth global:
                <strong>{{ $oauthConfiguredGlobally ? 'configurado no .env' : 'ainda nao configurado no .env' }}</strong>
            </p>
        </div>
    </div>

    <form method="POST" action="{{ route('drive-accounts.store') }}" class="surface panel-padding max-w-4xl space-y-5">
        @csrf
        @include('drive-accounts.partials.form', ['driveAccount' => null])
    </form>
</x-app-layout>
