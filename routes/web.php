<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\FirebaseAuthController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;

// Rutas públicas
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });

    // Rutas de autenticación
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');

    Route::post('/register', [FirebaseAuthController::class, 'register'])
        ->name('register.submit');

    Route::post('/login', [FirebaseAuthController::class, 'login'])
        ->name('login.submit');

    // Password Reset
    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');

    Route::post('/forgot-password', [FirebaseAuthController::class, 'forgotPassword'])
        ->name('password.email');

    // Google Auth
    Route::post('/auth/google/callback', [FirebaseAuthController::class, 'handleGoogleLogin'])
        ->name('login.google.callback');
});

// Rutas protegidas
Route::middleware(['auth'])->group(function () {
    // Dashboard según rol
    Route::get('/dashboard', function () {
        $user = auth()->user();
        $view = 'dashboard';
        
        if ($user->isAdmin()) {
            $view = 'admin.dashboard';
        } elseif ($user->isCaptain()) {
            $view = 'captain.dashboard';
        }
        
        return view($view, ['user' => $user->load('roles', 'playerProfile')]);
    })->name('dashboard');

    // Logout
    Route::post('/logout', [FirebaseAuthController::class, 'logout'])
        ->name('logout');

    // Perfil
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])
            ->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])
            ->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])
            ->name('profile.destroy');
    });

    // Rutas de miembros/jugadores
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

    // Rutas específicas de Admin
    Route::middleware(['role:super-admin'])->prefix('admin')->group(function () {
        Route::get('/roles', [RoleController::class, 'index'])->name('admin.roles.index');
        Route::post('/roles', [RoleController::class, 'store'])->name('admin.roles.store');
        Route::get('/system', [AdminController::class, 'system'])->name('admin.system');
    });

    // Rutas específicas de Capitán
    Route::middleware(['role:captain'])->prefix('captain')->group(function () {
        Route::get('/team', [CaptainController::class, 'team'])->name('captain.team');
        Route::get('/stats', [CaptainController::class, 'stats'])->name('captain.stats');
    });
});