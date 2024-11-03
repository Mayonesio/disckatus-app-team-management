<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\Auth\FirebaseAuthController;
use Kreait\Firebase\Auth as FirebaseAuth;

use App\Http\Controllers\Api\FirebaseConfigController;

Route::get('/auth/firebase-config', [FirebaseConfigController::class, 'getConfig']);

Route::post('/auth/login', [FirebaseAuthController::class, 'handleApiLogin']);

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

//Rutas de Diagnóstico (Públicas)
//-----------------------------------------------------------------------
Route::prefix('debug')->group(function () {
    // Prueba básica de conexión
    Route::get('/ping', function () {
        return ['message' => 'pong', 'timestamp' => now()];
    });

    // Estado general del sistema
    Route::get('/status', function () {
        return [
            'app_path' => app_path(),
            'models_exist' => [
                'Role' => class_exists(\App\Models\Role::class),
                'Skill' => class_exists(\App\Models\Skill::class),
                'PlayerProfile' => class_exists(\App\Models\PlayerProfile::class)
            ],
            'database_connection' => try_catch_null(fn() => \DB::connection()->getPdo()),
            'tables' => try_catch_null(fn() => \DB::select('SHOW TABLES')),
            'model_counts' => [
                'roles' => try_catch_null(fn() => \App\Models\Role::count()),
                'skills' => try_catch_null(fn() => \App\Models\Skill::count())
            ]
        ];
    });

    // Diagnóstico de Firebase
    Route::get('/firebase', function () {
        $credentialsPath = storage_path('storage/app/firebase/credentials.json');
        return [
            'credentials_path' => $credentialsPath,
            'credentials_exist' => file_exists($credentialsPath),
            'credentials_readable' => is_readable($credentialsPath),
            'storage_path' => storage_path(),
            'base_path' => base_path(),
            'directory_contents' => array_diff(scandir(storage_path('app')), ['.', '..']),
            'firebase_directory_exists' => is_dir(storage_path('app/firebase')),
        ];
    });
});
Route::prefix('auth')->group(function () {
    Route::get('/firebase-config', [FirebaseConfigController::class, 'getConfig']);
    Route::post('/google/callback', [FirebaseAuthController::class, 'handleGoogleLogin']);
});
//Rutas de Autenticación
//-----------------------------------------------------------------------
Route::prefix('auth')->group(function () {
    // Rutas públicas de autenticación
    Route::post('/login', [FirebaseAuthController::class, 'login']);
    
    // Configuración de Firebase (pública)
    Route::get('/firebase-config', function () {
        return response()->json([
            'apiKey' => env('FIREBASE_API'),
            'authDomain' => env('FIREBASE_AUTH_DOMAIN'),
            'projectId' => env('FIREBASE_PROJECT_ID'),
            'storageBucket' => env('FIREBASE_STORAGE_BUCKET'),
            'messagingSenderId' => env('FIREBASE_MESSAGING_SENDER_ID'),
            'appId' => env('FIREBASE_APP_ID'),
            'measurementId' => env('FIREBASE_MEASUREMENT_ID')
        ]);
    });

    Route::get('/debug/firebase-config', function () {
        $config = [
            'apiKey' => env('FIREBASE_API_KEY'),
            'authDomain' => env('FIREBASE_AUTH_DOMAIN'),
            'projectId' => env('FIREBASE_PROJECT_ID'),
            'storageBucket' => env('FIREBASE_STORAGE_BUCKET'),
            'messagingSenderId' => env('FIREBASE_MESSAGING_SENDER_ID'),
            'appId' => env('FIREBASE_APP_ID'),
            'measurementId' => env('FIREBASE_MEASUREMENT_ID')
        ];
    
        return [
            'config' => $config,
            'env_vars' => [
                'FIREBASE_API_KEY' => env('FIREBASE_API_KEY') ? 'set' : 'missing',
                'FIREBASE_API' => env('FIREBASE_API') ? 'set (should be removed)' : 'not present',
            ],
            'credentials_file' => [
                'exists' => file_exists(storage_path('app/firebase/credentials.json')),
                'path' => storage_path('app/firebase/credentials.json'),
            ]
        ];
    });

    // Ruta de prueba de login
    Route::post('/test-login', function (Request $request) {
        try {
            $auth = app(FirebaseAuth::class);
            $token = $request->input('idToken');
            $verifiedToken = $auth->verifyIdToken($token);
            
            return [
                'status' => 'success',
                'token_verified' => true,
                'uid' => $verifiedToken->claims()->get('sub'),
                'email' => $verifiedToken->claims()->get('email'),
            ];
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    });

    // Rutas protegidas de autenticación
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [FirebaseAuthController::class, 'logout']);
        Route::get('/user', [FirebaseAuthController::class, 'user']);
    });
});

//Rutas Protegidas de la Aplicación
//-----------------------------------------------------------------------
Route::middleware('auth:sanctum')->group(function () {
    // Rutas de Roles
    Route::prefix('roles')->group(function () {
        Route::get('/', function () {
            return \App\Models\Role::all();
        });
        Route::post('/', function (Request $request) {
            return \App\Models\Role::create($request->all());
        });
    });

    // Rutas de Skills
    Route::prefix('skills')->group(function () {
        Route::get('/', function () {
            return \App\Models\Skill::all();
        });
        Route::post('/', function (Request $request) {
            return \App\Models\Skill::create($request->all());
        });
    });

    // Ruta de verificación de datos
    Route::get('/check-data', function () {
        return [
            'roles' => \App\Models\Role::count(),
            'skills' => \App\Models\Skill::count(),
            'roles_list' => \App\Models\Role::select('name', 'slug')->get(),
            'skills_list' => \App\Models\Skill::select('name', 'description')->get(),
        ];
    });

    // Ruta de prueba de base de datos
    Route::get('/test/database', [TestController::class, 'checkDatabase']);
});
Route::post('/auth/google/callback', [FirebaseAuthController::class, 'handleGoogleLogin']);
