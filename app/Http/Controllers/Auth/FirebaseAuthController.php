<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB; 
class FirebaseAuthController extends Controller
{
    public function login(Request $request)
{
    $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
        $request->session()->regenerate();
        
        $user = Auth::user();
        $user->load('roles');

        Log::info('Login tradicional exitoso', [
            'user_id' => $user->id,
            'roles' => $user->roles->pluck('slug')
        ]);

        return redirect($this->getRedirectPath($user));
    }

    return back()
        ->withInput($request->only('email', 'remember'))
        ->withErrors(['email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.']);
}
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Asignar rol según si es el primer usuario o no
        if (User::count() === 1) {
            $role = Role::firstOrCreate(
                ['slug' => 'super-admin'],
                ['name' => 'Super Admin', 'description' => 'Control total del sistema']
            );
        } else {
            $role = Role::firstOrCreate(
                ['slug' => 'player'],
                ['name' => 'Player', 'description' => 'Jugador del equipo']
            );
        }

        $user->roles()->attach($role->id);

        Auth::login($user);

        return redirect()->route('dashboard');
    }

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
    
            $user = User::where('email', $email)->first();
            
            DB::beginTransaction();
            try {
                if (!$user) {
                    $user = User::create([
                        'name' => $name,
                        'email' => $email,
                        'firebase_uid' => $uid,
                        'avatar' => $picture
                    ]);
    
                    $isFirstUser = User::count() === 1;
                    $roleSlug = $isFirstUser ? 'super-admin' : 'player';
                    $role = Role::where('slug', $roleSlug)->first();
                    
                    if ($role) {
                        $user->roles()->attach($role->id, [
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
    
                    Log::info('Nuevo usuario creado', [
                        'user_id' => $user->id,
                        'role' => $roleSlug
                    ]);
                } else {
                    $user->firebase_uid = $uid;
                    $user->avatar = $picture;
                    $user->save();
    
                    if (!$user->roles()->exists()) {
                        $role = Role::where('slug', 'player')->first();
                        if ($role) {
                            $user->roles()->attach($role->id, [
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                        }
                    }
                }
    
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
    
            Auth::login($user);
            $user->load('roles');
            
            Log::info('Login exitoso', [
                'user_id' => $user->id,
                'roles' => $user->roles->pluck('slug'),
                'highest_role' => $user->getHighestRole()?->slug
            ]);
    
            return response()->json([
                'status' => 'success',
                'redirect' => $this->getRedirectPath($user)
            ]);
    
        } catch (\Exception $e) {
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

    protected function getRedirectPath($user)
    {
        if (!$user->roles()->exists()) {
            return route('dashboard');
        }
    
        $highestRole = $user->getHighestRole();
        if (!$highestRole) {
            return route('dashboard');
        }
    
        switch ($highestRole->slug) {
            case 'super-admin':
                return route('admin.dashboard');
            case 'captain':
                return route('captain.dashboard');
            default:
                return route('dashboard');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}