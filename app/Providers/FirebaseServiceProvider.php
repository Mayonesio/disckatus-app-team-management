<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth as FirebaseAuth;

class FirebaseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(FirebaseAuth::class, function ($app) {
            $factory = (new Factory)->withServiceAccount(
                base_path('app/firebase/credentials.json')
            );
            return $factory->createAuth();
        });
    }
}