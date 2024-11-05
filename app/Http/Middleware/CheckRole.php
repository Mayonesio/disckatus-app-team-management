<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        Log::info('CheckRole middleware: Checking roles', [
            'required_roles' => $roles,
            'user_authenticated' => auth()->check(),
            'user_id' => auth()->id()
        ]);

        if (!$request->user()) {
            Log::warning('CheckRole: No authenticated user');
            return redirect('/login');
        }

        if (!$request->user()->hasAnyRole($roles)) {
            Log::warning('CheckRole: User does not have required roles', [
                'user_roles' => $request->user()->roles->pluck('slug'),
                'required_roles' => $roles
            ]);

            if ($request->expectsJson()) {
                return response()->json(['error' => 'No autorizado'], 403);
            }
            
            return redirect()->route('dashboard')
                ->with('error', 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}