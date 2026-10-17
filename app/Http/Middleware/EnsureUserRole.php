<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();
        $userRole = trim((string) ($user?->role ?? '')) ?: 'public';

        if (! $user || ! in_array($userRole, $roles, true)) {
            abort(403, 'Akses ditolak.');
        }

        return $next($request);
    }
}
