<x-guest-layout>
    <section class="auth-shell">
        <article class="auth-card surface-strong panel-padding">
            <div class="auth-card__head">
                <p class="eyebrow">Acesso restrito</p>
                <p class="auth-lead">
                    Entre para gerenciar igrejas, documentos, fotos e auditoria com o mesmo ambiente visual do portal.
                </p>
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

            <form class="auth-form" method="POST" action="{{ route('login') }}">
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

                <div class="auth-form__row">
                    <label class="checkbox-line">
                        <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                        <span>Manter sessao neste dispositivo</span>
                    </label>
                </div>

                <button class="button button-primary auth-form__submit" type="submit">Entrar no painel</button>
            </form>
        </article>
    </section>
</x-guest-layout>
