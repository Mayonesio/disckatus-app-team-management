<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class FirebaseAuthController extends Controller
{
    public function handleGoogleLogin(Request $request)
    {
        try {
            Log::info('Google login attempt', ['request' => $request->all()]);
            
            $idToken = $request->input('idToken');
            $verifiedToken = app('firebase.auth')->verifyIdToken($idToken);
            
            $uid = $verifiedToken->claims()->get('sub');
            $email = $verifiedToken->claims()->get('email');
            $name = $verifiedToken->claims()->get('name', '');
            $picture = $verifiedToken->claims()->get('picture', '');

            // Buscar o crear usuario
            $user = User::where('email', $email)->withTrashed()->first();

            if (!$user) {
                $user = new User();
                $user->email = $email;
                $user->name = $name;
                $user->firebase_uid = $uid;
                $user->avatar = $picture;
                $user->save();

                // Asignar rol para nuevo usuario
                $isFirstUser = User::count() === 1;
                if ($isFirstUser) {
                    $role = Role::where('slug', 'super-admin')->first();
                } else {
                    $role = Role::where('slug', 'player')->first();
                }

                if ($role) {
                    $user->roles()->attach($role->id);
                    Log::info('Rol asignado a nuevo usuario', [
                        'user' => $user->id,
                        'role' => $role->slug
                    ]);
                }
            } else {
                // Restaurar usuario si estaba eliminado
                if ($user->trashed()) {
                    $user->restore();
                }
                // Actualizar datos
                $user->firebase_uid = $uid;
                $user->avatar = $picture;
                $user->save();
            }

            Auth::login($user);

            $userRoles = $user->roles()->pluck('slug')->toArray();
            Log::info('Usuario autenticado', [
                'user_id' => $user->id,
                'roles' => $userRoles
            ]);

            // Determinar redirección basada en rol
            $redirectTo = '/dashboard';
            if ($user->isAdmin()) {
                $redirectTo = '/admin/dashboard';
            } elseif ($user->isCaptain()) {
                $redirectTo = '/captain/dashboard';
            }

            return response()->json([
                'status' => 'success',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $userRoles,
                    'highest_role' => $user->getHighestRole()?->slug
                ],
                'redirect' => $redirectTo
            ]);

        } catch (Exception $e) {
            Log::error('Error en autenticación Google:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Error de autenticación con Google: ' . $e->getMessage()
            ], 401);
        }
    }
}