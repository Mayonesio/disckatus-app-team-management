<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class FirebaseConfigController extends Controller
{
    public function getConfig()
    {
        $config = [
            'apiKey' => env('FIREBASE_API_KEY'),
            'authDomain' => env('FIREBASE_AUTH_DOMAIN'),
            'projectId' => env('FIREBASE_PROJECT_ID'),
            'storageBucket' => env('FIREBASE_STORAGE_BUCKET'),
            'messagingSenderId' => env('FIREBASE_MESSAGING_SENDER_ID'),
            'appId' => env('FIREBASE_APP_ID'),
            'measurementId' => env('FIREBASE_MEASUREMENT_ID')
        ];

        // Verificar configuración
        foreach ($config as $key => $value) {
            if (empty($value)) {
                \Log::error("Firebase config missing: {$key}");
            }
        }

        return response()->json($config);
    }
}