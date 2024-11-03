<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Kreait\Firebase\Auth as FirebaseAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\User; 


class VerifyFirebaseToken
{
    protected $auth;

    public function __construct(FirebaseAuth $auth)
    {
        $this->auth = $auth;
    }

    public function handle(Request $request, Closure $next)
    {
        try {
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json(['error' => 'No se proporcionó token de autenticación'], 401);
            }

            // Verificar el token con Firebase
            $verifiedIdToken = $this->auth->verifyIdToken($token);
            $uid = $verifiedIdToken->claims()->get('sub');
            $email = $verifiedIdToken->claims()->get('email');

            \Log::info('Token verificado exitosamente', [
                'uid' => $uid,
                'email' => $email
            ]);

            // Buscar o crear el usuario en la base de datos local
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $verifiedIdToken->claims()->get('name', ''),
                    'firebase_uid' => $uid,
                    'password' => Hash::make(Str::random(16))
                ]
            );

            Auth::setUser($user);
            return $next($request);

        } catch (\Exception $e) {
            \Log::error('Error verificando token Firebase:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Token inválido: ' . $e->getMessage()], 401);
        }
    }
}