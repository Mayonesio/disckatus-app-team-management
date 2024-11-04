<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\FirebaseAuthController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CaptainController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Auth;
// Rutas públicas y de autenticación
Route::middleware('guest')->group(function () {
    // Redirección principal
    Route::get('/', function () {
        return redirect()->route('login');
    });

    // Rutas de autenticación básica
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');

    // Rutas POST de autenticación
    Route::post('/register', [FirebaseAuthController::class, 'register'])
        ->name('register.submit');
    Route::post('/login', [FirebaseAuthController::class, 'login'])
        ->name('login.submit');

    // Recuperación de contraseña
    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');
    Route::post('/forgot-password', [FirebaseAuthController::class, 'forgotPassword'])
        ->name('password.email');

    // Autenticación con Google
    Route::post('/auth/google/callback', [FirebaseAuthController::class, 'handleGoogleLogin'])
        ->name('login.google.callback');
});

// Rutas protegidas - Requieren autenticación
Route::middleware(['auth'])->group(function () {
    // Ruta de cierre de sesión
    Route::post('/logout', [FirebaseAuthController::class, 'logout'])
        ->name('logout');

    // Dashboard principal - Redirecciona según rol
    Route::get('/dashboard', function () {
        $user = auth()->user();
        $highestRole = $user->getHighestRole();

        if (!$highestRole) {
            return view('dashboard', ['user' => $user]);
        }

        // Redirección basada en rol
        switch ($highestRole->slug) {
            case 'super-admin':
                return redirect()->route('admin.dashboard');
            case 'captain':
                return redirect()->route('captain.dashboard');
            default:
                return view('dashboard', ['user' => $user->load('roles', 'playerProfile')]);
        }
    })->name('dashboard');

    // Rutas de perfil - Accesibles para todos los usuarios autenticados
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])
            ->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])
            ->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])
            ->name('profile.destroy');
    });

    // Rutas de administrador
    Route::middleware(['role:super-admin'])->prefix('admin')->group(function () {
        // Dashboard de admin
        Route::get('/dashboard', [AdminController::class, 'dashboard'])
            ->name('admin.dashboard');

        // Gestión de roles
        Route::get('/roles', [RoleController::class, 'index'])
            ->name('admin.roles.index');
        Route::post('/roles', [RoleController::class, 'store'])
            ->name('admin.roles.store');

        // Configuración del sistema
        Route::get('/system', [AdminController::class, 'system'])
            ->name('admin.system');

        // Gestión de usuarios
        Route::get('/users', [AdminController::class, 'users'])
            ->name('admin.users');
        Route::patch('/users/{user}/role', [AdminController::class, 'updateUserRole'])
            ->name('admin.users.update-role');
    });

    // Rutas de capitán
    Route::middleware(['role:captain'])->prefix('captain')->group(function () {
        Route::get('/dashboard', [CaptainController::class, 'dashboard'])
            ->name('captain.dashboard');
        Route::get('/team', [CaptainController::class, 'team'])
            ->name('captain.team');
        Route::get('/stats', [CaptainController::class, 'stats'])
            ->name('captain.stats');
    });

    // Rutas de gestión de miembros - Accesible para admin y capitán
    Route::middleware(['role:super-admin,captain'])->prefix('members')->group(function () {
        Route::get('/', [PlayerController::class, 'index'])
            ->name('members.index');
        Route::get('/{user}', [PlayerController::class, 'show'])
            ->name('members.show');
        Route::get('/{user}/edit', [PlayerController::class, 'edit'])
            ->name('members.edit');
        Route::put('/{user}', [PlayerController::class, 'update'])
            ->name('members.update');
    });
});