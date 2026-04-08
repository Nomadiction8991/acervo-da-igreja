<div class="grid gap-5 md:grid-cols-2">
    <div class="field-block">
        <label class="field-block__label" for="igreja_id">Igreja</label>
        <select id="igreja_id" name="igreja_id" class="field-control">
            @foreach ($igrejas as $igreja)
                <option value="{{ $igreja->id }}" @selected(old('igreja_id', $tarefa?->igreja_id ?? request('igreja_id')) == $igreja->id)>{{ $igreja->nome_fantasia }}</option>
            @endforeach
        </select>
    </div>
    <div class="field-block">
        <label class="field-block__label" for="user_id">Responsavel</label>
        <select id="user_id" name="user_id" class="field-control">
            <option value="">Sem responsavel</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" @selected(old('user_id', $tarefa?->user_id) == $user->id)>{{ $user->name }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="field-block">
    <label class="field-block__label" for="titulo">Titulo</label>
    <input id="titulo" name="titulo" type="text" class="field-control" value="{{ old('titulo', $tarefa?->titulo) }}" required>
</div>

<div class="grid gap-5 md:grid-cols-3">
    <div class="field-block">
        <label class="field-block__label" for="status">Status</label>
        <select id="status" name="status" class="field-control">
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(old('status', $tarefa?->status?->value) === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
    </div>
    <div class="field-block">
        <label class="field-block__label" for="prioridade">Prioridade</label>
        <select id="prioridade" name="prioridade" class="field-control">
            @foreach ($priorities as $priority)
                <option value="{{ $priority->value }}" @selected(old('prioridade', $tarefa?->prioridade?->value) === $priority->value)>{{ $priority->label() }}</option>
            @endforeach
        </select>
    </div>
    <div class="field-block">
        <label class="field-block__label" for="due_at">Prazo</label>
        <input id="due_at" name="due_at" type="datetime-local" class="field-control" value="{{ old('due_at', $tarefa?->due_at?->format('Y-m-d\\TH:i')) }}">
    </div>
</div>

<div class="field-block">
    <label class="field-block__label" for="descricao">Descricao</label>
    <textarea id="descricao" name="descricao" rows="4" class="field-control">{{ old('descricao', $tarefa?->descricao) }}</textarea>
</div>

<div class="flex gap-3">
    <button type="submit" class="button button-primary">{{ $tarefa ? 'Salvar alteracoes' : 'Criar tarefa' }}</button>
    <a href="{{ $tarefa ? route('tarefas.show', $tarefa) : route('tarefas.index') }}" class="button button-muted">Cancelar</a>
</div>
