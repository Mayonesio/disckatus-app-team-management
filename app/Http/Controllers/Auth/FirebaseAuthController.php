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

       try {
           if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
               $request->session()->regenerate();
               
               $user = User::with('roles')->find(Auth::id());
               
               Log::info('Login tradicional exitoso', [
                   'user_id' => $user->id,
                   'roles' => $user->roles->pluck('slug'),
                   'roles_count' => $user->roles->count(),
                   'highest_role' => $user->getHighestRole()?->slug
               ]);

               return redirect($this->getRedirectPath($user));
           }

           return back()
               ->withInput($request->only('email', 'remember'))
               ->withErrors(['email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.']);
       } catch (\Exception $e) {
           Log::error('Error en login tradicional:', [
               'error' => $e->getMessage(),
               'trace' => $e->getTraceAsString()
           ]);
           return back()->withErrors(['error' => 'Error al iniciar sesión. Por favor, inténtalo de nuevo.']);
       }
   }

   public function register(Request $request)
   {
       $request->validate([
           'name' => 'required|string|max:255',
           'email' => 'required|string|email|max:255|unique:users',
           'password' => 'required|string|confirmed|min:8',
       ]);

       Log::info('Iniciando registro de usuario', ['email' => $request->email]);

       DB::beginTransaction();
       try {
           $user = User::create([
               'name' => $request->name,
               'email' => $request->email,
               'password' => Hash::make($request->password),
           ]);

           Log::info('Usuario creado exitosamente', ['user_id' => $user->id]);

           // Asignar rol según si es el primer usuario o no
           $isFirstUser = User::count() === 1;
           $roleSlug = $isFirstUser ? 'super-admin' : 'player';
           
           $role = Role::firstOrCreate(
               ['slug' => $roleSlug],
               [
                   'name' => $isFirstUser ? 'Super Admin' : 'Player',
                   'description' => $isFirstUser ? 'Control total del sistema' : 'Jugador del equipo'
               ]
           );

           $user->roles()->attach($role->id, [
               'created_at' => now(),
               'updated_at' => now()
           ]);

           Log::info('Rol asignado al usuario', [
               'user_id' => $user->id,
               'role' => $role->slug,
               'is_first_user' => $isFirstUser
           ]);

           DB::commit();

           $user->refresh();
           $user->load('roles');
           Auth::login($user);

           return redirect()->route('dashboard');
       } catch (\Exception $e) {
           DB::rollback();
           Log::error('Error en registro:', [
               'error' => $e->getMessage(),
               'trace' => $e->getTraceAsString()
           ]);
           return back()->withErrors(['error' => 'Error al crear el usuario. Por favor, inténtalo de nuevo.']);
       }
   }

   public function handleGoogleLogin(Request $request)
   {
       try {
           Log::info('Google login attempt iniciado', [
               'request' => $request->all()
           ]);
           
           $idToken = $request->input('idToken');
           $verifiedToken = app('firebase.auth')->verifyIdToken($idToken);
           
           $uid = $verifiedToken->claims()->get('sub');
           $email = $verifiedToken->claims()->get('email');
           $name = $verifiedToken->claims()->get('name', '');
           $picture = $verifiedToken->claims()->get('picture', '');

           Log::info('Token Firebase verificado', [
               'email' => $email,
               'uid' => $uid
           ]);

           // Eager loading de roles desde el inicio
           $user = User::with('roles')->where('email', $email)->first();
           
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
                   $role = Role::where('slug', $roleSlug)->firstOrFail();
                   
                   $user->roles()->attach($role->id, [
                       'created_at' => now(),
                       'updated_at' => now()
                   ]);

                   Log::info('Nuevo usuario Google creado', [
                       'user_id' => $user->id,
                       'role' => $roleSlug,
                       'is_first_user' => $isFirstUser,
                       'roles_count' => $user->roles()->count()
                   ]);
               } else {
                   Log::info('Usuario Google existente', [
                       'user_id' => $user->id,
                       'current_roles' => $user->roles->pluck('slug')
                   ]);

                   $user->firebase_uid = $uid;
                   $user->avatar = $picture;
                   $user->save();

                   if (!$user->roles()->exists()) {
                       $role = Role::where('slug', 'player')->firstOrFail();
                       $user->roles()->attach($role->id, [
                           'created_at' => now(),
                           'updated_at' => now()
                       ]);
                       Log::info('Rol player asignado a usuario existente', [
                           'user_id' => $user->id
                       ]);
                   }
               }

               DB::commit();

               // Recargar usuario con sus roles
               $user->refresh();
               $user->load('roles');
               
               Auth::login($user);

               $redirectPath = $this->getRedirectPath($user);
               Log::info('Login Google exitoso', [
                   'user_id' => $user->id,
                   'roles' => $user->roles->pluck('slug')->toArray(),
                   'highest_role' => $user->getHighestRole()?->slug,
                   'roles_count' => $user->roles()->count(),
                   'auth_check' => Auth::check(),
                   'redirect_path' => $redirectPath
               ]);

               return response()->json([
                   'status' => 'success',
                   'redirect' => $redirectPath,
                   'user' => [
                       'id' => $user->id,
                       'name' => $user->name,
                       'roles' => $user->roles->pluck('slug')
                   ]
               ]);

           } catch (\Exception $e) {
               DB::rollback();
               Log::error('Error en transacción DB:', [
                   'error' => $e->getMessage(),
                   'trace' => $e->getTraceAsString()
               ]);
               throw $e;
           }

       } catch (\Exception $e) {
           Log::error('Error en autenticación Google:', [
               'error' => $e->getMessage(),
               'trace' => $e->getTraceAsString(),
               'email' => $email ?? 'no_email'
           ]);
           
           return response()->json([
               'status' => 'error',
               'message' => 'Error de autenticación con Google: ' . $e->getMessage()
           ], 401);
       }
   }

   protected function getRedirectPath($user)
   {
       Log::info('Calculando ruta de redirección', [
           'user_id' => $user->id,
           'has_roles' => $user->roles()->exists(),
           'roles' => $user->roles->pluck('slug')
       ]);

       if (!$user->roles()->exists()) {
           Log::info('Usuario sin roles, redirigiendo a dashboard general');
           return route('dashboard');
       }
   
       $highestRole = $user->getHighestRole();
       if (!$highestRole) {
           Log::info('No se pudo determinar rol más alto, redirigiendo a dashboard general');
           return route('dashboard');
       }
   
       Log::info('Determinando ruta por rol', [
           'highest_role' => $highestRole->slug
       ]);

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
       $userId = Auth::id();
       Auth::logout();
       $request->session()->invalidate();
       $request->session()->regenerateToken();
       
       Log::info('Usuario desconectado', ['user_id' => $userId]);
       
       return redirect('/');
   }
}