<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Log;

class SimpleFirebaseAuth
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function handle(Request $request, Closure $next)
    {
        Log::info('Iniciando verificación de token Firebase');
        
        try {
            $token = str_replace('Bearer ', '', $request->header('Authorization'));
            
            if (empty($token)) {
                Log::error('Token no proporcionado');
                return response()->json(['error' => 'Token no proporcionado'], 401);
            }

            $verified = $this->firebase->verifyToken($token);
            
            if (!$verified) {
                Log::error('Token inválido');
                return response()->json(['error' => 'Token inválido'], 401);
            }

            Log::info('Token verificado correctamente');
            return $next($request);
            
        } catch (\Exception $e) {
            Log::error('Error en middleware Firebase', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Error de autenticación',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}