<x-app-layout>
    @section('title', 'Meu perfil | Acervo da Igreja')

    @if (session('status') === 'profile-updated')
        <div class="status-banner status-banner--success">
            Perfil atualizado com sucesso.
        </div>
    @endif

    @if (session('status') === 'password-updated')
        <div class="status-banner status-banner--success">
            Senha alterada com sucesso.
        </div>
    @endif

    <section class="grid gap-6 xl:grid-cols-3">
            <article class="surface panel-padding">
                <p class="eyebrow">Dados basicos</p>
                <h2 class="section-title mt-4">Informacoes da conta</h2>

                <form class="mt-6 space-y-4" method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')

                    <label class="field-block">
                        <span class="field-block__label">Nome</span>
                        <input class="field-control" type="text" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <p class="field-errors">{{ $message }}</p>
                        @enderror
                    </label>

                    <label class="field-block">
                        <span class="field-block__label">Email</span>
                        <input class="field-control" type="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <p class="field-errors">{{ $message }}</p>
                        @enderror
                    </label>

                    <button class="button button-primary w-full justify-center" type="submit">Salvar perfil</button>
                </form>
            </article>

            <article class="surface panel-padding">
                <p class="eyebrow">Senha</p>
                <h2 class="section-title mt-4">Atualizar credencial</h2>

                <form class="mt-6 space-y-4" method="POST" action="{{ route('password.update') }}">
                    @csrf
                    @method('PUT')

                    <label class="field-block">
                        <span class="field-block__label">Senha atual</span>
                        <input class="field-control" type="password" name="current_password" required>
                        @error('current_password', 'updatePassword')
                            <p class="field-errors">{{ $message }}</p>
                        @enderror
                    </label>

                    <label class="field-block">
                        <span class="field-block__label">Nova senha</span>
                        <input class="field-control" type="password" name="password" required>
                        @error('password', 'updatePassword')
                            <p class="field-errors">{{ $message }}</p>
                        @enderror
                    </label>

                    <label class="field-block">
                        <span class="field-block__label">Confirmar nova senha</span>
                        <input class="field-control" type="password" name="password_confirmation" required>
                    </label>

                    <button class="button button-primary w-full justify-center" type="submit">Atualizar senha</button>
                </form>
            </article>

            <article class="surface panel-padding">
                <p class="eyebrow">Zona sensivel</p>
                <h2 class="section-title mt-4">Inativar conta</h2>
                <p class="mt-4 text-sm leading-6 text-[var(--text-secondary)]">
                    Esta acao desativa sua conta e encerra a sessao. Voce nao conseguira acessar o sistema ate que um administrador reative sua conta.
                </p>

                <form class="mt-6 space-y-4" method="POST" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('DELETE')

                    <label class="field-block">
                        <span class="field-block__label">Confirme sua senha</span>
                        <input class="field-control" type="password" name="password" required>
                        @error('password', 'userDeletion')
                            <p class="field-errors">{{ $message }}</p>
                        @enderror
                    </label>

                    <button class="button button-ghost w-full justify-center" type="submit">Inativar minha conta</button>
                </form>
            </article>
    </section>
</x-app-layout>
