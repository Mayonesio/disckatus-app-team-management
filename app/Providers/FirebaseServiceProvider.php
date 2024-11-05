<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth as FirebaseAuth;
use App\Services\FirebaseService;

class FirebaseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Registrar FirebaseService como singleton
        $this->app->singleton(FirebaseService::class, function ($app) {
            return new FirebaseService();
        });

        // Registrar FirebaseAuth como singleton
        $this->app->singleton(FirebaseAuth::class, function ($app) {
            return (new Factory)
                ->withServiceAccount(base_path('app/firebase/credentials.json'))
                ->createAuth();
        });

        // No usar alias para evitar conflictos
        $this->app->bind('firebase.auth', function ($app) {
            return $app->make(FirebaseAuth::class);
        });
    }
}