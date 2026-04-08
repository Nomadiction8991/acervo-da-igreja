<div class="grid gap-5 md:grid-cols-2">
    <div class="field-block">
        <label class="field-block__label" for="name">Nome</label>
        <input id="name" name="name" type="text" class="field-control" value="{{ old('name', $user?->name) }}" required>
    </div>
    <div class="field-block">
        <label class="field-block__label" for="email">Email</label>
        <input id="email" name="email" type="email" class="field-control" value="{{ old('email', $user?->email) }}" required>
    </div>
</div>

<div class="grid gap-5 md:grid-cols-2">
    <div class="field-block">
        <label class="field-block__label" for="password">Senha {{ $user ? '(opcional)' : '' }}</label>
        <input id="password" name="password" type="password" class="field-control" {{ $user ? '' : 'required' }}>
    </div>
    <div class="field-block">
        <label class="field-block__label" for="password_confirmation">Confirmacao da senha</label>
        <input id="password_confirmation" name="password_confirmation" type="password" class="field-control" {{ $user ? '' : 'required' }}>
    </div>
</div>

<div class="flex flex-wrap gap-4">
    <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="is_admin" value="1" @checked(old('is_admin', $user?->is_admin))>
        Administrador
    </label>
    <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user?->is_active ?? true))>
        Usuario ativo
    </label>
</div>

<div>
    <p class="field-block__label mb-3">Permissoes</p>
    <div class="grid gap-4 md:grid-cols-2">
        @foreach ($permissions as $modulo => $items)
            <div class="border border-[var(--border-subtle)] rounded-xl p-4">
                <p class="font-semibold mb-3">{{ ucfirst($modulo) }}</p>
                <div class="space-y-2">
                    @foreach ($items as $permission)
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="permission_ids[]" value="{{ $permission->id }}"
                                   @checked(collect(old('permission_ids', $user?->permissions->pluck('id')->all() ?? []))->contains($permission->id))>
                            {{ $permission->slug }}
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="flex gap-3">
    <button type="submit" class="button button-primary">{{ $user ? 'Salvar alteracoes' : 'Criar usuario' }}</button>
    <a href="{{ $user ? route('users.show', $user) : route('users.index') }}" class="button button-muted">Cancelar</a>
</div>
