<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

final class TagController extends Controller
{
    use AuthorizesRequests;

    public function index(): View
    {
        $tags = Tag::query()
            ->withCount('igrejas')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('tags.index', compact('tags'));
    }

    public function create(): View
    {
        return view('tags.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
            'cor' => ['nullable', 'string', 'regex:/^#[0-9A-F]{6}$/i'],
        ]);

        $this->ensureUniqueSlug($validated['nome']);

        Tag::create($validated);

        return redirect()->route('tags.index')->with('success', 'Tag criada com sucesso.');
    }

    public function edit(Tag $tag): View
    {
        return view('tags.edit', compact('tag'));
    }

    public function update(Request $request, Tag $tag): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
            'cor' => ['nullable', 'string', 'regex:/^#[0-9A-F]{6}$/i'],
        ]);

        $this->ensureUniqueSlug($validated['nome'], $tag);

        $tag->update($validated);

        return redirect()->route('tags.index')->with('success', 'Tag atualizada com sucesso.');
    }

    public function destroy(Tag $tag): RedirectResponse
    {
        $tag->delete();

        return redirect()->route('tags.index')->with('success', 'Tag removida com sucesso.');
    }

    private function ensureUniqueSlug(string $nome, ?Tag $ignore = null): void
    {
        $slug = Str::slug($nome);

        $query = Tag::query()
            ->where('slug', $slug);

        if ($ignore !== null) {
            $query->whereKeyNot($ignore->id);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'nome' => 'Já existe uma tag com um nome equivalente a este slug.',
            ]);
        }
    }
}
