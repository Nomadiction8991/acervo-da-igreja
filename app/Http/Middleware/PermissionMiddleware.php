<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class PermissionMiddleware
{
    /**
     * @param Closure(Request): Response $next
     */
    public function handle(Request $request, Closure $next, string $slug): Response
    {
        $user = $request->user();

        abort_if($user === null || ! $user->hasPermission($slug), 403);

        return $next($request);
    }
}
