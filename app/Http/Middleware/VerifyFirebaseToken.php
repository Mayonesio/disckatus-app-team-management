<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Log;

class VerifyFirebaseToken
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function handle(Request $request, Closure $next)
    {
        try {
            Log::info('Verificando token Firebase');
            
            $token = str_replace('Bearer ', '', $request->header('Authorization'));
            if (empty($token)) {
                return response()->json(['error' => 'Token no proporcionado'], 401);
            }

            $verifiedToken = $this->firebaseService->verifyToken($token);
            if (!$verifiedToken) {
                return response()->json(['error' => 'Token inválido'], 401);
            }

            Log::info('Token verificado correctamente');
            return $next($request);

        } catch (\Exception $e) {
            Log::error('Error en verificación de token:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Error de autenticación',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}