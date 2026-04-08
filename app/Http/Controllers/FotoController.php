<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Foto\StoreFotoRequest;
use App\Http\Requests\Foto\UpdateFotoRequest;
use App\Models\Foto;
use App\Models\Igreja;
use App\Services\FotoService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use RuntimeException;

final class FotoController extends Controller
{
    use AuthorizesRequests;

    public function index(Igreja $igreja): View
    {
        $this->authorize('viewAny', Foto::class);

        $fotos = Foto::query()
            ->with('driveAccount')
            ->where('igreja_id', $igreja->id)
            ->orderByDesc('is_principal')
            ->orderBy('ordem')
            ->paginate(20);

        return view('fotos.index', compact('igreja', 'fotos'));
    }

    public function create(Igreja $igreja): View
    {
        $this->authorize('create', Foto::class);

        return view('fotos.create', compact('igreja'));
    }

    public function store(
        StoreFotoRequest $request,
        Igreja $igreja,
        FotoService $service,
    ): RedirectResponse
    {
        $this->authorize('create', Foto::class);

        /** @var array<int, \Illuminate\Http\UploadedFile> $files */
        $files = $request->file('fotos', []);

        $service->storeMany($igreja, $files, $request->boolean('is_public', true));

        return redirect()
            ->route('fotos.index', $igreja)
            ->with('success', 'Fotos enviadas com sucesso.');
    }

    public function show(Igreja $igreja, Foto $foto): View
    {
        $this->ensureOwnership($igreja, $foto);
        $this->authorize('view', $foto);
        // Carregar driveAccount se não estiver carregado (pode ser null)
        $foto->load('driveAccount');

        return view('fotos.show', compact('igreja', 'foto'));
    }

    public function edit(Igreja $igreja, Foto $foto): View
    {
        $this->ensureOwnership($igreja, $foto);
        $this->authorize('update', $foto);

        return view('fotos.edit', compact('igreja', 'foto'));
    }

    public function update(
        UpdateFotoRequest $request,
        Igreja $igreja,
        Foto $foto,
        FotoService $service,
    ): RedirectResponse
    {
        $this->ensureOwnership($igreja, $foto);
        $this->authorize('update', $foto);

        $service->update($foto, [
            'is_public' => $request->boolean('is_public'),
            'is_principal' => $request->boolean('is_principal'),
        ]);

        return redirect()
            ->route('fotos.show', [$igreja, $foto])
            ->with('success', 'Foto atualizada com sucesso.');
    }

    public function destroy(Igreja $igreja, Foto $foto, FotoService $service): RedirectResponse
    {
        $this->ensureOwnership($igreja, $foto);
        $this->authorize('delete', $foto);

        $service->delete($foto);

        return redirect()
            ->route('fotos.index', $igreja)
            ->with('success', 'Foto removida com sucesso.');
    }

    public function syncToDrive(Igreja $igreja, Foto $foto, FotoService $service): RedirectResponse
    {
        $this->ensureOwnership($igreja, $foto);
        $this->authorize('update', $foto);

        try {
            $service->triggerManualSync($foto);
        } catch (RuntimeException $exception) {
            return back()->withErrors([
                'drive_sync' => $exception->getMessage(),
            ]);
        }

        return back()->with('success', 'Sincronizacao manual com Google Drive iniciada para a foto.');
    }

    private function ensureOwnership(Igreja $igreja, Foto $foto): void
    {
        if ($foto->igreja_id !== $igreja->id) {
            abort(404);
        }
    }
}
