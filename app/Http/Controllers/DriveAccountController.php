<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\DriveAccount\StoreDriveAccountRequest;
use App\Http\Requests\DriveAccount\UpdateDriveAccountRequest;
use App\Models\DriveAccount;
use App\Services\DriveAccountService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use RuntimeException;

final class DriveAccountController extends Controller
{
    use AuthorizesRequests;

    public function index(): View
    {
        $this->authorize('viewAny', DriveAccount::class);

        $driveAccounts = DriveAccount::query()
            ->withCount('fileSyncLogs')
            ->orderBy('nome')
            ->paginate(20);

        return view('drive-accounts.index', compact('driveAccounts'));
    }

    public function create(): View
    {
        $this->authorize('create', DriveAccount::class);

        return view('drive-accounts.create', [
            'oauthConfiguredGlobally' => $this->oauthConfiguredGlobally(),
        ]);
    }

    public function store(
        StoreDriveAccountRequest $request,
        DriveAccountService $service,
    ): RedirectResponse {
        $this->authorize('create', DriveAccount::class);

        $driveAccount = $service->store([
            ...$request->safe()->except(['client_secret', 'refresh_token']),
            'client_secret' => $request->input('client_secret'),
            'refresh_token' => $request->input('refresh_token'),
        ]);

        return redirect()
            ->route('drive-accounts.show', $driveAccount)
            ->with('success', 'Conta Google Drive criada com sucesso.');
    }

    public function show(DriveAccount $driveAccount): View
    {
        $this->authorize('view', $driveAccount);

        $driveAccount->load([
            'fileSyncLogs' => static fn ($query) => $query
                ->with(['documento', 'user'])
                ->latest('attempted_at')
                ->limit(10),
        ]);

        return view('drive-accounts.show', [
            'driveAccount' => $driveAccount,
            'oauthAvailable' => app(DriveAccountService::class)->oauthIsConfigured($driveAccount),
        ]);
    }

    public function edit(DriveAccount $driveAccount): View
    {
        $this->authorize('update', $driveAccount);

        return view('drive-accounts.edit', compact('driveAccount'));
    }

    public function update(
        UpdateDriveAccountRequest $request,
        DriveAccount $driveAccount,
        DriveAccountService $service,
    ): RedirectResponse {
        $this->authorize('update', $driveAccount);

        $service->update($driveAccount, [
            ...$request->safe()->except(['client_secret', 'refresh_token']),
            'client_secret' => $request->input('client_secret'),
            'refresh_token' => $request->input('refresh_token'),
        ]);

        return redirect()
            ->route('drive-accounts.show', $driveAccount)
            ->with('success', 'Conta Google Drive atualizada com sucesso.');
    }

    public function destroy(
        DriveAccount $driveAccount,
        DriveAccountService $service,
    ): RedirectResponse {
        $this->authorize('delete', $driveAccount);

        $service->delete($driveAccount);

        return redirect()
            ->route('drive-accounts.index')
            ->with('success', 'Conta Google Drive removida com sucesso.');
    }

    public function testConnection(
        DriveAccount $driveAccount,
        DriveAccountService $service,
    ): RedirectResponse {
        $this->authorize('testConnection', $driveAccount);

        try {
            $result = $service->testConnection($driveAccount);
        } catch (RuntimeException $exception) {
            return back()->withErrors([
                'drive_account' => $exception->getMessage(),
            ]);
        }

        $message = 'Conexao validada com sucesso.';

        if (($result['email'] ?? null) !== null) {
            $message .= ' Conta autenticada: '.$result['email'].'.';
        }

        return redirect()
            ->route('drive-accounts.show', $driveAccount)
            ->with('success', $message);
    }

    public function redirectToGoogle(
        Request $request,
        DriveAccount $driveAccount,
        DriveAccountService $service,
    ): RedirectResponse {
        $this->authorize('update', $driveAccount);

        if (! $service->oauthIsConfigured($driveAccount)) {
            return back()->withErrors([
                'drive_account' => 'Configure o cliente OAuth do Google Drive no .env ou na conta antes de conectar.',
            ]);
        }

        $state = bin2hex(random_bytes(20));
        $request->session()->put('google_drive_oauth_states.'.$state, [
            'drive_account_id' => $driveAccount->id,
        ]);

        return redirect()->away($service->authorizationUrl($state, $driveAccount));
    }

    public function handleGoogleCallback(
        Request $request,
        DriveAccountService $service,
    ): RedirectResponse {
        $state = $request->query('state');

        if (! is_string($state) || $state === '') {
            return redirect()
                ->route('drive-accounts.index')
                ->withErrors(['drive_account' => 'Retorno OAuth invalido: state ausente.']);
        }

        /** @var array{drive_account_id?: int}|null $payload */
        $payload = $request->session()->get('google_drive_oauth_states.'.$state);
        $request->session()->forget('google_drive_oauth_states.'.$state);

        if ($payload === null || ! isset($payload['drive_account_id'])) {
            return redirect()
                ->route('drive-accounts.index')
                ->withErrors(['drive_account' => 'A tentativa de conexao expirou. Tente novamente.']);
        }

        /** @var DriveAccount $driveAccount */
        $driveAccount = DriveAccount::query()->findOrFail($payload['drive_account_id']);
        $this->authorize('update', $driveAccount);

        $error = $request->query('error');

        if (is_string($error) && $error !== '') {
            return redirect()
                ->route('drive-accounts.show', $driveAccount)
                ->withErrors(['drive_account' => 'O Google retornou um erro de autorizacao: '.$error]);
        }

        $code = $request->query('code');

        if (! is_string($code) || $code === '') {
            return redirect()
                ->route('drive-accounts.show', $driveAccount)
                ->withErrors(['drive_account' => 'O Google nao retornou o codigo de autorizacao.']);
        }

        try {
            $result = $service->connectWithAuthorizationCode($driveAccount, $code);
        } catch (RuntimeException $exception) {
            return redirect()
                ->route('drive-accounts.show', $driveAccount)
                ->withErrors(['drive_account' => $exception->getMessage()]);
        }

        $message = 'Conta Google Drive conectada com sucesso via OAuth.';

        if (($result['email'] ?? null) !== null) {
            $message .= ' Conta autenticada: '.$result['email'].'.';
        }

        return redirect()
            ->route('drive-accounts.show', $driveAccount)
            ->with('success', $message);
    }

    private function oauthConfiguredGlobally(): bool
    {
        /** @var DriveAccountService $service */
        $service = app(DriveAccountService::class);

        return $service->oauthIsConfigured();
    }
}
