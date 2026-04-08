<x-guest-layout>
    <section class="grid gap-6 place-items-center">
        <article class="surface panel-padding w-full max-w-lg">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="eyebrow">Login real</p>
                    <h2 class="section-title mt-2">Identificacao da equipe</h2>
                </div>
            </div>

            @if (session('status'))
                <div class="status-banner status-banner--success mt-6">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="status-banner status-banner--danger mt-6">
                    Confira os campos e tente novamente.
                </div>
            @endif

            <form class="mt-8 space-y-4" method="POST" action="{{ route('login') }}">
                @csrf

                <label class="field-block">
                    <span class="field-block__label">Email</span>
                    <input
                        class="field-control"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="Seu email"
                        required
                        autofocus
                        autocomplete="username"
                    >
                    @error('email')
                        <p class="field-errors">{{ $message }}</p>
                    @enderror
                </label>

                <label class="field-block">
                    <span class="field-block__label">Senha</span>
                    <input
                        class="field-control"
                        type="password"
                        name="password"
                        placeholder="Sua senha segura"
                        required
                        autocomplete="current-password"
                    >
                    @error('password')
                        <p class="field-errors">{{ $message }}</p>
                    @enderror
                </label>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <label class="checkbox-line">
                        <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                        <span>Manter sessao neste dispositivo</span>
                    </label>
                </div>

                <button class="button button-primary w-full justify-center" type="submit">Entrar no painel</button>
            </form>
        </article>
    </section>
</x-guest-layout>
