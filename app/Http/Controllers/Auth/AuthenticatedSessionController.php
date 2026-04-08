<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Services\AuditLogService;
use App\Support\ChurchDirectory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(ChurchDirectory $directory): View
    {
        return view('auth.access', [
            'stats' => $directory->portalStats(),
            'featuredChurch' => $directory->all()->first(),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request, AuditLogService $auditLogService): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        /** @var User|null $user */
        $user = $request->user();

        if ($user !== null) {
            $auditLogService->log(
                action: 'login',
                module: 'auth',
                entity: User::class,
                entityId: $user->id,
                oldValues: null,
                newValues: ['email' => $user->email],
            );
        }

        return redirect()->intended(route('admin.dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request, AuditLogService $auditLogService): RedirectResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        if ($user !== null) {
            $auditLogService->log(
                action: 'logout',
                module: 'auth',
                entity: User::class,
                entityId: $user->id,
                oldValues: ['email' => $user->email],
                newValues: null,
            );
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('portal.index');
    }
}
