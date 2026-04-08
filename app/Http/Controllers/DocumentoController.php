<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Documento\StoreDocumentoRequest;
use App\Http\Requests\Documento\UpdateDocumentoRequest;
use App\Models\Documento;
use App\Models\DriveAccount;
use App\Models\GrupoDocumento;
use App\Models\Igreja;
use App\Models\User;
use App\Services\DocumentoService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class DocumentoController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Documento::class);

        $search = $request->input('q');

        $documentos = Documento::query()
            ->with(['igreja', 'grupoDocumento', 'user', 'driveAccount'])
            ->search($search)
            ->when($request->filled('igreja_id'), static function ($query) use ($request): void {
                $query->where('igreja_id', $request->integer('igreja_id'));
            })
            ->when($request->filled('sync_status'), static function ($query) use ($request): void {
                $status = $request->string('sync_status')->toString();

                if ($status === 'sem_drive') {
                    $query->whereNull('drive_file_id');

                    return;
                }

                $query->where('sync_status', $status);
            })
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('documentos.index', [
            'documentos' => $documentos,
            'igrejas' => Igreja::query()->orderBy('nome_fantasia')->get(),
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Documento::class);

        return view('documentos.create', $this->formData());
    }

    public function store(StoreDocumentoRequest $request, DocumentoService $service): RedirectResponse
    {
        $this->authorize('create', Documento::class);

        /** @var User $user */
        $user = $request->user();

        $documento = $service->store(
            data: [
                ...$request->safe()->except('arquivo'),
                'publico' => $request->boolean('publico'),
            ],
            file: $request->file('arquivo'),
            user: $user,
        );

        return redirect()
            ->route('documentos.show', $documento)
            ->with('success', 'Documento criado com sucesso.');
    }

    public function show(Documento $documento): View
    {
        $this->authorize('view', $documento);

        $documento->load(['igreja', 'grupoDocumento', 'user', 'fileSyncLogs.user', 'fileSyncLogs.driveAccount']);
        $documento->loadMissing('driveAccount');

        return view('documentos.show', compact('documento'));
    }

    public function edit(Documento $documento): View
    {
        $this->authorize('update', $documento);

        return view('documentos.edit', [
            ...$this->formData(),
            'documento' => $documento,
        ]);
    }

    public function update(
        UpdateDocumentoRequest $request,
        Documento $documento,
        DocumentoService $service,
    ): RedirectResponse {
        $this->authorize('update', $documento);

        $service->update(
            documento: $documento,
            data: [
                ...$request->safe()->except('arquivo'),
                'publico' => $request->boolean('publico'),
            ],
            file: $request->file('arquivo'),
        );

        return redirect()
            ->route('documentos.show', $documento)
            ->with('success', 'Documento atualizado com sucesso.');
    }

    public function destroy(Documento $documento, DocumentoService $service): RedirectResponse
    {
        $this->authorize('delete', $documento);

        $service->delete($documento);

        return redirect()
            ->route('documentos.index')
            ->with('success', 'Documento removido com sucesso.');
    }

    public function syncToDrive(Documento $documento, DocumentoService $service): RedirectResponse
    {
        $this->authorize('update', $documento);

        $documento->loadMissing('driveAccount');
        $driveAccount = $documento->driveAccount;

        if (! $driveAccount instanceof DriveAccount) {
            $message = 'Selecione uma conta Google Drive no documento antes de sincronizar.';
            $documento->update([
                'sync_status' => 'error',
                'sync_error' => $message,
            ]);

            return back()->withErrors(['drive_sync' => $message]);
        }

        if (! is_string($driveAccount->refresh_token) || trim($driveAccount->refresh_token) === '') {
            $message = 'A conta Google Drive selecionada ainda nao esta conectada. Conecte via OAuth e tente novamente.';
            $documento->update([
                'sync_status' => 'error',
                'sync_error' => $message,
            ]);

            return back()->withErrors(['drive_sync' => $message]);
        }

        $service->triggerManualSync($documento);

        return back()->with('success', 'Sincronizacao manual com Google Drive iniciada.');
    }

    /**
     * @return array{
     *     igrejas: \Illuminate\Database\Eloquent\Collection<int, Igreja>,
     *     grupos: \Illuminate\Database\Eloquent\Collection<int, GrupoDocumento>,
     *     driveAccounts: \Illuminate\Database\Eloquent\Collection<int, DriveAccount>
     * }
     */
    private function formData(): array
    {
        return [
            'igrejas' => Igreja::query()->orderBy('nome_fantasia')->get(),
            'grupos' => GrupoDocumento::query()->orderBy('nome')->get(),
            'driveAccounts' => DriveAccount::query()
                ->whereNotNull('refresh_token')
                ->orderBy('nome')
                ->get(),
        ];
    }
}
