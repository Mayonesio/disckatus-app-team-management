<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!$request->user() || !$request->user()->hasAnyRole($roles)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'No autorizado'], 403);
            }
            return redirect()->route('dashboard')
                ->with('error', 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}